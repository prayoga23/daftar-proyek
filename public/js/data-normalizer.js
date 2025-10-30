/**
 * Data Normalizer untuk Aplikasi Web
 * Memisahkan data yang tergabung dengan koma menjadi baris terpisah
 */

class DataNormalizer {
    constructor() {
        this.init();
    }

    init() {
        // Jalankan normalisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            this.normalizeTableData();
        });

        // Jalankan normalisasi saat Livewire update
        document.addEventListener('livewire:updated', () => {
            this.normalizeTableData();
        });
    }

    /**
     * Normalisasi data tabel
     */
    normalizeTableData() {
        const table = document.querySelector('table tbody');
        if (!table) return;

        const rows = table.querySelectorAll('tr');
        
        rows.forEach((row, index) => {
            // Skip jika sudah dinormalisasi
            if (row.classList.contains('normalized')) return;
            
            // Tandai sel yang memiliki newline
            this.markNewlineCells(row);
            
            this.normalizeRow(row, index);
        });
    }

    /**
     * Tandai sel yang memiliki newline
     */
    markNewlineCells(row) {
        const cells = row.querySelectorAll('td');
        if (cells.length < 6) return;

        // Cek kolom yang bisa memiliki multiple values
        const checkColumns = [2, 3, 4, 5]; // OWNER, JENIS PEKERJAAN, YANG MENGERJAKAN, STATUS PEKERJAAN
        
        checkColumns.forEach(colIndex => {
            const cell = cells[colIndex];
            const text = cell.textContent.trim();
            
            if (text.includes('\n')) {
                cell.setAttribute('data-has-newline', 'true');
                cell.setAttribute('data-can-normalize', 'true');
            }
        });
    }

    /**
     * Normalisasi satu baris data
     */
    normalizeRow(originalRow, index) {
        const cells = originalRow.querySelectorAll('td');
        if (cells.length < 6) return; // Pastikan ada 6 kolom

        // Ambil data dari setiap sel
        const no = cells[0].textContent.trim();
        const proyek = cells[1].textContent.trim();
        const owner = cells[2].textContent.trim();
        const jenisPekerjaan = cells[3].textContent.trim();
        const yangMengerjakan = cells[4].textContent.trim();
        const statusPekerjaan = cells[5].textContent.trim();
        const actions = cells[6].innerHTML; // Simpan HTML actions

        // Split data berdasarkan newline
        const owners = this.splitData(owner);
        const jenisPekerjaans = this.splitData(jenisPekerjaan);
        const yangMengerjakans = this.splitData(yangMengerjakan);
        const statusPekerjaans = this.splitData(statusPekerjaan);

        // Tentukan jumlah maksimal entries
        const maxEntries = Math.max(
            owners.length,
            jenisPekerjaans.length,
            yangMengerjakans.length,
            statusPekerjaans.length
        );

        // Jika hanya ada 1 entry, tidak perlu dinormalisasi
        if (maxEntries <= 1) {
            originalRow.classList.add('normalized');
            return;
        }

        // Hapus baris asli
        originalRow.remove();

        // Buat baris baru untuk setiap entry
        for (let i = 0; i < maxEntries; i++) {
            const newRow = this.createNormalizedRow(
                no,
                proyek,
                owners[i] || owners[0] || '',
                jenisPekerjaans[i] || '',
                yangMengerjakans[i] || '',
                statusPekerjaans[i] || '',
                actions,
                i === 0 // Show actions only on first row
            );

            // Insert baris baru
            const tableBody = document.querySelector('table tbody');
            tableBody.appendChild(newRow);
        }
    }

    /**
     * Split data berdasarkan newline dan bersihkan whitespace
     */
    splitData(data) {
        if (!data) return [''];
        return data.split('\n').map(item => item.trim()).filter(item => item);
    }

    /**
     * Buat baris baru yang sudah dinormalisasi
     */
    createNormalizedRow(no, proyek, owner, jenisPekerjaan, yangMengerjakan, statusPekerjaan, actions, showActions) {
        const row = document.createElement('tr');
        
        // Alternating row colors
        const isEven = parseInt(no) % 2 === 0;
        row.className = `border-b ${isEven ? 'bg-white' : 'bg-green-50'} normalized`;

        // Cell untuk NO (hanya tampil di baris pertama)
        const noCell = document.createElement('td');
        noCell.className = 'px-3 py-4 border border-gray-300';
        noCell.textContent = showActions ? no : '';
        row.appendChild(noCell);

        // Cell untuk PROYEK (hanya tampil di baris pertama)
        const proyekCell = document.createElement('td');
        proyekCell.className = 'px-3 py-4 border border-gray-300';
        proyekCell.textContent = showActions ? proyek : '';
        row.appendChild(proyekCell);

        // Cell untuk OWNER
        const ownerCell = document.createElement('td');
        ownerCell.className = 'px-3 py-4 border border-gray-300';
        ownerCell.textContent = owner;
        row.appendChild(ownerCell);

        // Cell untuk JENIS PEKERJAAN
        const jenisCell = document.createElement('td');
        jenisCell.className = 'px-3 py-4 border border-gray-300';
        jenisCell.textContent = jenisPekerjaan;
        row.appendChild(jenisCell);

        // Cell untuk YANG MENGERJAKAN
        const yangMengerjakanCell = document.createElement('td');
        yangMengerjakanCell.className = 'px-3 py-4 border border-gray-300';
        yangMengerjakanCell.textContent = yangMengerjakan;
        row.appendChild(yangMengerjakanCell);

        // Cell untuk STATUS PEKERJAAN
        const statusCell = document.createElement('td');
        statusCell.className = 'px-3 py-4 border border-gray-300';
        statusCell.textContent = statusPekerjaan;
        row.appendChild(statusCell);

        // Cell untuk ACTIONS (hanya tampil di baris pertama)
        const actionsCell = document.createElement('td');
        actionsCell.className = 'px-3 py-3 space-y-1 space-x-3 border border-gray-300';
        if (showActions) {
            actionsCell.innerHTML = actions;
        }
        row.appendChild(actionsCell);

        return row;
    }

    /**
     * Reset normalisasi (kembalikan ke tampilan asli)
     */
    resetNormalization() {
        const normalizedRows = document.querySelectorAll('tr.normalized');
        normalizedRows.forEach(row => {
            row.classList.remove('normalized');
        });
        
        // Reload halaman untuk mengembalikan data asli
        window.location.reload();
    }

    /**
     * Toggle normalisasi on/off
     */
    toggleNormalization() {
        const button = document.getElementById('toggle-normalization');
        const isNormalized = document.querySelector('tr.normalized');
        
        if (isNormalized) {
            this.resetNormalization();
            button.textContent = 'Normalisasi Data';
            button.classList.remove('bg-red-500', 'hover:bg-red-600');
            button.classList.add('bg-blue-500', 'hover:bg-blue-600');
        } else {
            this.normalizeTableData();
            button.textContent = 'Reset Data';
            button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            button.classList.add('bg-red-500', 'hover:bg-red-600');
        }
    }
}

// Inisialisasi DataNormalizer
const dataNormalizer = new DataNormalizer();

// Export untuk penggunaan global
window.DataNormalizer = dataNormalizer;
