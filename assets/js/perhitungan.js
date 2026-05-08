// assets/js/perhitungan.js
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('table-body');
    const btnTambah = document.getElementById('btn-tambah');

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function tambahBarisKosong() {
        const row = tbody.insertRow();
        row.insertCell(0).innerHTML = '<input type="text" name="id_barang[]" placeholder="ID (opsional)" class="form-control-sm">';
        row.insertCell(1).innerHTML = '<input type="text" name="nama_barang[]" placeholder="Nama Barang" class="form-control-sm" required>';
        for (let i = 0; i < 4; i++) {
            row.insertCell(2 + i).innerHTML = '<input type="number" step="0.01" name="nilai[]" class="form-control-sm" placeholder="0" required>';
        }
    }

    // Kosongkan tbody
    tbody.innerHTML = '';

    // Ambil data dari window (variabel global dataBarang)
    let barangList = window.dataBarang;
    if (!barangList || !Array.isArray(barangList)) {
        barangList = [];
        console.error('dataBarang tidak tersedia atau bukan array');
    }

    if (barangList.length > 0) {
        // Loop setiap barang dan buat baris tabel
        barangList.forEach(barang => {
            const id = barang.id_barang || barang.id || '';
            const nama = barang.nama_barang || barang.nama || 'Tidak diketahui';
            const row = tbody.insertRow();
            // Kolom ID Barang (readonly)
            row.insertCell(0).innerHTML = `<input type="text" name="id_barang[]" value="${escapeHtml(id)}" readonly class="form-control-sm" style="background:#e9ecef;">`;
            // Kolom Nama Barang (readonly)
            row.insertCell(1).innerHTML = `<input type="text" name="nama_barang[]" value="${escapeHtml(nama)}" readonly class="form-control-sm" style="background:#e9ecef;">`;
            // Kolom nilai kriteria (input)
            for (let i = 0; i < 4; i++) {
                row.insertCell(2 + i).innerHTML = '<input type="number" step="0.01" name="nilai[]" class="form-control-sm" placeholder="0" required>';
            }
        });
    } else {
        // Tampilkan pesan jika belum ada barang
        const row = tbody.insertRow();
        const cell = row.insertCell(0);
        cell.colSpan = 6;
        cell.textContent = '⚠️ Belum ada data barang. Silakan tambah barang melalui menu Manajemen Barang.';
        cell.style.textAlign = 'center';
        cell.style.padding = '20px';
        cell.style.backgroundColor = '#fff3cd';
        cell.style.color = '#856404';
    }

    // Event tombol tambah manual
    if (btnTambah) {
        btnTambah.addEventListener('click', tambahBarisKosong);
    }
});