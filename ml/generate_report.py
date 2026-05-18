import os
import json
import argparse
import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import mysql.connector

# openpyxl used for styling the Excel output
from openpyxl import load_workbook
from openpyxl.styles import Font, PatternFill, Alignment
from openpyxl.utils import get_column_letter
from openpyxl.chart import BarChart, Reference


def get_db_connection():
    host = os.getenv('DB_HOST', 'mysql-spk')
    user = os.getenv('DB_USER', 'spk_user')
    password = os.getenv('DB_PASSWORD', 'spk_password')
    database = os.getenv('DB_NAME', 'spk_inventaris')
    port = int(os.getenv('DB_PORT', '3306'))

    return mysql.connector.connect(
        host=host,
        user=user,
        password=password,
        database=database,
        port=port
    )


def load_data(conn, matriks_id):
    query = '''
        SELECT
            h.id_barang,
            h.nama_barang,
            h.skor,
            COALESCE(h.nilai_s, 0) AS nilai_s,
            COALESCE(h.nilai_v, 0) AS nilai_v,
            COALESCE(h.nilai_yi, 0) AS nilai_yi,
            COALESCE(h.nilai_vi_saw, 0) AS nilai_vi_saw,
            COALESCE(h.nilai_vi_topsis, 0) AS nilai_vi_topsis,
            COALESCE(h.rank_moora, 0) AS rank_moora,
            COALESCE(h.rank_saw, 0) AS rank_saw,
            COALESCE(h.rank_topsis, 0) AS rank_topsis,
            COALESCE(h.consensus_rank, 0) AS consensus_rank,
            b.stok_tersedia,
            b.stok_minimum,
            COALESCE(b.usia_pakai_bulan, 0) AS usia_pakai_bulan,
            b.status_garansi,
            b.tgl_beli,
            b.spesifikasi
        FROM hasil AS h
        LEFT JOIN barang AS b ON h.id_barang = b.id_barang
        WHERE h.id_matriks = %s
    '''
    df = pd.read_sql(query, conn, params=(matriks_id,))
    return df


def encode_status(status):
    if status == 'Aktif':
        return 2
    if status == 'Hampir Habis':
        return 1
    return 0


def build_ml_report(df, matriks_id):
    if df.empty:
        return None

    features = df[[
        'skor',
        'nilai_s',
        'nilai_v',
        'nilai_yi',
        'nilai_vi_saw',
        'nilai_vi_topsis',
        'rank_moora',
        'rank_saw',
        'rank_topsis',
        'consensus_rank',
        'stok_tersedia',
        'stok_minimum',
        'usia_pakai_bulan'
    ]].fillna(0)

    if len(df) == 1:
        n_clusters = 1
    else:
        n_clusters = min(3, len(df))

    scaler = StandardScaler()
    X = scaler.fit_transform(features)

    km = KMeans(n_clusters=n_clusters, random_state=42, n_init=10)
    labels = km.fit_predict(X)
    df['ml_cluster'] = labels

    cluster_summary = df.groupby('ml_cluster')[['consensus_rank', 'skor', 'nilai_v', 'nilai_yi']].mean().reset_index()
    cluster_summary = cluster_summary.sort_values('consensus_rank')

    label_names = ['High Priority', 'Medium Priority', 'Low Priority']
    cluster_label_map = {}
    for i, cluster in enumerate(cluster_summary['ml_cluster'].tolist()):
        cluster_label_map[cluster] = label_names[i]

    df['procurement_priority'] = df['ml_cluster'].map(cluster_label_map)

    report_rows = []
    for _, row in df.iterrows():
        report_rows.append({
            'nama_barang': row['nama_barang'],
            'procurement_priority': row['procurement_priority'],
            'consensus_rank': int(row['consensus_rank']),
            'skor': float(row['skor']),
            'nilai_v': float(row['nilai_v']),
            'nilai_yi': float(row['nilai_yi']),
            'nilai_vi_saw': float(row['nilai_vi_saw']),
            'nilai_vi_topsis': float(row['nilai_vi_topsis']),
            'rank_moora': int(row['rank_moora']),
            'rank_saw': int(row['rank_saw']),
            'rank_topsis': int(row['rank_topsis'])
        })

    report_df = pd.DataFrame(report_rows)
    return report_df, cluster_summary, cluster_label_map


def save_report(report_df, output_path, cluster_summary, label_map):
    # Write initial sheets via pandas
    with pd.ExcelWriter(output_path, engine='openpyxl') as writer:
        report_df.to_excel(writer, sheet_name='Procurement Report', index=False)
        summary_df = pd.DataFrame([
            {
                'ml_cluster': int(cluster),
                'priority_label': label_map.get(cluster, 'Unknown'),
                'avg_consensus_rank': float(row['consensus_rank']),
                'avg_score': float(row['skor']),
                'avg_nilai_v': float(row['nilai_v']),
                'avg_nilai_yi': float(row['nilai_yi'])
            }
            for cluster, row in cluster_summary.set_index('ml_cluster').iterrows()
        ])
        summary_df.to_excel(writer, sheet_name='Cluster Summary', index=False)

    # Open workbook with openpyxl to apply styling and add explanatory sheet
    wb = load_workbook(output_path)

    def style_sheet(ws):
        # Bold header and fill
        header_fill = PatternFill(start_color='FFD966', end_color='FFD966', fill_type='solid')
        for cell in list(ws[1]):
            cell.font = Font(bold=True)
            cell.fill = header_fill
            cell.alignment = Alignment(horizontal='center', vertical='center')

        # Adjust column widths
        for col_idx, column_cells in enumerate(ws.columns, 1):
            max_length = 0
            for cell in column_cells:
                try:
                    val = str(cell.value) if cell.value is not None else ''
                except Exception:
                    val = ''
                if len(val) > max_length:
                    max_length = len(val)
            adjusted_width = (max_length + 2)
            ws.column_dimensions[get_column_letter(col_idx)].width = adjusted_width

    # Style the two main sheets
    if 'Procurement Report' in wb.sheetnames:
        style_sheet(wb['Procurement Report'])
    if 'Cluster Summary' in wb.sheetnames:
        style_sheet(wb['Cluster Summary'])

    # Add an Explanations sheet with method descriptions and metric meanings
    if 'Explanations' in wb.sheetnames:
        ex_ws = wb['Explanations']
    else:
        ex_ws = wb.create_sheet('Explanations')

    explanations = [
        ("Dokumentasi Laporan ML - SPK Inventaris", ""),
        ("Ringkasan:", "Laporan ini mengelompokkan barang berdasarkan hasil SPK dan ML clustering. 'High Priority' berarti prioritas pengadaan tinggi."),
        ("Skor (skor):", "Skor akhir dari metode WP (Weighted Product) - nilai kepentingan agregat per barang."),
        ("Nilai S (nilai_s):", "Produk berbobot dari atribut (sebelum normalisasi) pada metode WP."),
        ("Nilai V (nilai_v):", "Nilai normalisasi dari 'nilai_s' yang menunjukkan perbandingan proporsional antar barang; V lebih besar = lebih direkomendasikan."),
        ("Nilai Yi (nilai_yi):", "Nilai objektif (hasil transformasi WP) yang digunakan untuk perankingan internal; mirip dengan V tetapi dipakai untuk metode tertentu.") ,
        ("Nilai VI SAW/TOPSIS (nilai_vi_saw / nilai_vi_topsis):", "Nilai V yang dihitung oleh metode SAW / TOPSIS; memberikan perspektif alternatif terhadap prioritas."),
        ("Rank MOORA/SAW/TOPSIS (rank_moora/rank_saw/rank_topsis):", "Peringkat hasil pada masing-masing metode MOORA, SAW, dan TOPSIS. Peringkat 1 adalah terbaik."),
        ("Consensus Rank:", "Peringkat gabungan berdasarkan jumlah rekomendasi pada tiap metode; semakin kecil nilainya semakin tinggi prioritas.") ,
        ("Procurement Priority:", "Label yang dihasilkan ML clustering: High/Medium/Low Priority.") ,
        ("Interpretasi:", "Gunakan 'Consensus Rank' dan label ML bersama kondisi stok untuk mengambil keputusan pengadaan. Periksa juga 'usia_pakai_bulan' dan 'stok_tersedia'.")
    ]

    # write explanations into sheet
    row = 1
    for title, text in explanations:
        ex_ws.cell(row=row, column=1, value=title)
        ex_ws.cell(row=row, column=2, value=text)
        ex_ws.cell(row=row, column=1).font = Font(bold=True)
        ex_ws.cell(row=row, column=2).alignment = Alignment(wrap_text=True)
        row += 1

    # Adjust column widths for explanations
    ex_ws.column_dimensions['A'].width = 30
    ex_ws.column_dimensions['B'].width = 90

    # Create a simple bar chart from Cluster Summary (avg_score)
    try:
        cs = wb['Cluster Summary']
        # find number of data rows
        max_row = cs.max_row
        if max_row >= 2:
            data_ref = Reference(cs, min_col=cs.max_column-3, min_row=1, max_row=max_row)
            cats_ref = Reference(cs, min_col=2, min_row=2, max_row=max_row)
            chart = BarChart()
            chart.title = 'Cluster - Avg Consensus Rank by Cluster'
            chart.add_data(data_ref, titles_from_data=True)
            chart.set_categories(cats_ref)
            chart.height = 8
            chart.width = 16
            cs.add_chart(chart, 'H2')
    except Exception:
        pass

    wb.save(output_path)


def main():
    parser = argparse.ArgumentParser(description='Generate ML procurement report from SPK results.')
    parser.add_argument('--matriks-id', type=int, default=1, help='ID matriks to generate report from')
    parser.add_argument('--output', required=True, help='Output Excel report path')
    parser.add_argument('--json-output', required=False, help='JSON summary output path')
    args = parser.parse_args()

    conn = get_db_connection()
    try:
        df = load_data(conn, args.matriks_id)
        if df.empty:
            raise ValueError('Tidak ada data hasil untuk id_matriks=' + str(args.matriks_id))

        df['status_garansi_num'] = df['status_garansi'].apply(encode_status).fillna(0)
        report_df, cluster_summary, label_map = build_ml_report(df, args.matriks_id)
        if report_df is None:
            raise ValueError('Target data kosong setelah pemrosesan ML.')

        save_report(report_df, args.output, cluster_summary, label_map)

        result = {
            'success': True,
            'matriks_id': args.matriks_id,
            'output': args.output,
            'rows': report_df.to_dict(orient='records'),
            'cluster_summary': cluster_summary.to_dict(orient='records'),
            'label_map': label_map,
            'message': 'Laporan ML berhasil dibuat.'
        }
        if args.json_output:
            with open(args.json_output, 'w', encoding='utf-8') as f:
                json.dump(result, f, ensure_ascii=False, indent=2)

        print(json.dumps(result, ensure_ascii=False))
    finally:
        conn.close()


if __name__ == '__main__':
    main()
