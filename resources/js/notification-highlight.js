/**
 * Script untuk highlight row tabel saat user navigasi dari notifikasi.
 * Membaca query parameter ?highlight=ID lalu menambahkan kelas animasi pada row terkait.
 */
document.addEventListener('DOMContentLoaded', function () {
    highlightRowFromUrl();
});

// Jalankan juga saat Livewire navigasi (SPA)
document.addEventListener('livewire:navigated', function () {
    highlightRowFromUrl();
});

function highlightRowFromUrl() {
    const params = new URLSearchParams(window.location.search);
    const highlightId = params.get('highlight');

    if (!highlightId) {
        return;
    }

    // Tunggu sedikit agar tabel Filament selesai render
    setTimeout(() => {
        // Cari row berdasarkan wire:key yang mengandung record ID
        const rows = document.querySelectorAll('tr[wire\\:key]');
        let targetRow = null;

        rows.forEach(row => {
            const key = row.getAttribute('wire:key');
            // Format wire:key biasanya mengandung record ID
            if (key && (key.includes('.' + highlightId + '.') || key.endsWith('.' + highlightId))) {
                targetRow = row;
            }
        });

        // Fallback: cari berdasarkan data-id atau row index
        if (!targetRow) {
            const allRows = document.querySelectorAll('.fi-ta-row');
            allRows.forEach(row => {
                // Check apakah row mengandung record key
                const key = row.getAttribute('wire:key') || '';
                if (key.includes(highlightId)) {
                    targetRow = row;
                }
            });
        }

        if (targetRow) {
            // Tambahkan kelas highlight
            targetRow.classList.add('fi-ta-row-highlight');

            // Scroll ke row tersebut
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Hapus kelas setelah animasi selesai (3 detik)
            setTimeout(() => {
                targetRow.classList.remove('fi-ta-row-highlight');
            }, 3500);
        }

        // Bersihkan URL dari query param highlight
        const url = new URL(window.location);
        url.searchParams.delete('highlight');
        window.history.replaceState({}, '', url);
    }, 500);
}
