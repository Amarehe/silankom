{{-- CSS & JS untuk notifikasi: highlight row + read/unread styling --}}
<style>
    /* =============================================
       Animasi highlight row dari notifikasi
       ============================================= */
    @keyframes notification-highlight-pulse {
        0%   { background-color: rgba(245, 158, 11, 0.35); }
        25%  { background-color: rgba(245, 158, 11, 0.12); }
        50%  { background-color: rgba(245, 158, 11, 0.28); }
        75%  { background-color: rgba(245, 158, 11, 0.08); }
        100% { background-color: transparent; }
    }

    @keyframes notification-highlight-pulse-dark {
        0%   { background-color: rgba(245, 158, 11, 0.25); }
        25%  { background-color: rgba(245, 158, 11, 0.08); }
        50%  { background-color: rgba(245, 158, 11, 0.18); }
        75%  { background-color: rgba(245, 158, 11, 0.05); }
        100% { background-color: transparent; }
    }

    .fi-ta-row-highlight > td {
        animation: notification-highlight-pulse 3.5s ease-out forwards !important;
    }

    .dark .fi-ta-row-highlight > td {
        animation-name: notification-highlight-pulse-dark !important;
    }

    /* =============================================
       Notifikasi: Unread vs Read styling
       ============================================= */

    /* Unread: aksen border kiri + background + dot indicator */
    .fi-no-notification-unread-ctn {
        border-left: 3px solid rgb(245, 158, 11);
        background-color: rgba(245, 158, 11, 0.05);
        border-radius: 0.5rem;
        margin-bottom: 2px;
        position: relative;
        transition: background-color 0.2s ease;
    }

    .fi-no-notification-unread-ctn:hover {
        background-color: rgba(245, 158, 11, 0.10);
    }

    /* Dot indikator unread */
    .fi-no-notification-unread-ctn::before {
        content: '';
        position: absolute;
        top: 14px;
        right: 40px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: rgb(245, 158, 11);
        box-shadow: 0 0 6px rgba(245, 158, 11, 0.4);
        z-index: 10;
    }

    /* Unread: title lebih tebal */
    .fi-no-notification-unread-ctn .fi-no-notification-title {
        font-weight: 700 !important;
    }

    /* Read: lebih muted */
    .fi-no-notification-read-ctn {
        border-left: 3px solid transparent;
        opacity: 0.6;
        border-radius: 0.5rem;
        margin-bottom: 2px;
        transition: opacity 0.2s ease;
    }

    .fi-no-notification-read-ctn:hover {
        opacity: 0.85;
    }

    .fi-no-notification-read-ctn .fi-no-notification-title {
        font-weight: 400 !important;
    }

    /* Dark mode adjustments */
    .dark .fi-no-notification-unread-ctn {
        background-color: rgba(245, 158, 11, 0.08);
        border-left-color: rgb(251, 191, 36);
    }

    .dark .fi-no-notification-unread-ctn:hover {
        background-color: rgba(245, 158, 11, 0.14);
    }

    .dark .fi-no-notification-unread-ctn::before {
        background-color: rgb(251, 191, 36);
        box-shadow: 0 0 6px rgba(251, 191, 36, 0.5);
    }

    .dark .fi-no-notification-read-ctn {
        opacity: 0.5;
    }

    .dark .fi-no-notification-read-ctn:hover {
        opacity: 0.75;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => handleHighlightFromUrl());
    document.addEventListener('livewire:navigated', () => handleHighlightFromUrl());

    /**
     * Highlight row tabel berdasarkan ?highlight=ID di URL
     */
    function handleHighlightFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const highlightId = params.get('highlight');
        if (!highlightId) return;

        // Bersihkan URL langsung agar tidak trigger ulang
        const url = new URL(window.location);
        url.searchParams.delete('highlight');
        window.history.replaceState({}, '', url);

        // Coba beberapa kali karena tabel mungkin belum render
        const attempts = [200, 500, 1000, 1800, 3000];
        let found = false;

        attempts.forEach(delay => {
            setTimeout(() => {
                if (found) return;

                const rows = document.querySelectorAll('tr[wire\\:key]');
                rows.forEach(row => {
                    const key = row.getAttribute('wire:key') || '';
                    // Format Filament: {livewireId}.table.records.{recordId}
                    if (key.endsWith('.table.records.' + highlightId)) {
                        found = true;
                        row.classList.add('fi-ta-row-highlight');
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => row.classList.remove('fi-ta-row-highlight'), 4000);
                    }
                });
            }, delay);
        });
    }
</script>
