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

    /* Toast Notification Styles */
    .notification-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-left: 4px solid #10b981;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 16px;
        min-width: 300px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    }

    .notification-toast.dark-mode {
        background: #1f2937;
        color: white;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .notification-toast.hide {
        animation: slideOut 0.3s ease-out forwards;
    }

    .notification-toast-title {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 0.875rem;
    }

    .notification-toast-message {
        font-size: 0.8125rem;
        opacity: 0.9;
    }
</style>

<script>
    class NotificationHandler {
        constructor() {
            this.lastBadgeCounts = {};
            this.updateDebounce = {};
            this.init();
        }

        init() {
            console.log('[NotificationSound] Initialized globally');
            this.startPeriodicBadgeRefresh();
            this.watchBadgeChanges();
            this.setupEchoListener();
        }

        setupEchoListener() {
            if (typeof window.Echo === 'undefined') {
                console.log('[NotificationSound] Echo not available, using polling only');
                return;
            }

            const userId = @json(auth()->id() ?? null);
            if (!userId) return;

            window.Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    console.log('[NotificationSound] Notification received:', notification);
                    this.playSound();
                    this.showToast(
                        notification.data?.title || 'Notifikasi Baru',
                        notification.data?.message || 'Ada pembaruan'
                    );
                });
        }

        watchBadgeChanges() {
            setInterval(() => {
                const models = ['pengajuan', 'pengajuan-perbaikan', 'pengajuan-dukungan'];
                models.forEach(model => {
                    const badgeSelector = `[data-test="notification-badge-${model}"]`;
                    const badges = document.querySelectorAll(badgeSelector);

                    badges.forEach(badge => {
                        const count = parseInt(badge.textContent) || 0;
                        const lastCount = this.lastBadgeCounts[model] || 0;

                        if (count > lastCount && lastCount > 0) {
                            console.log(`[NotificationSound] Badge increased: ${model} (${lastCount} → ${count})`);
                            this.playSound();
                            this.showToast(
                                'Ada Notifikasi Baru',
                                this.getModelLabel(model) + ' telah diperbarui'
                            );
                        }
                        this.lastBadgeCounts[model] = count;
                    });
                });
            }, 3000);
        }

        getModelLabel(model) {
            const labels = {
                'pengajuan': 'Pengajuan Peminjaman',
                'pengajuan-perbaikan': 'Pengajuan Perbaikan',
                'pengajuan-dukungan': 'Pengajuan Dukungan',
            };
            return labels[model] || model;
        }

        playSound() {
            try {
                const audio = new Audio('/sounds/notification.wav');
                audio.volume = 0.5;
                audio.play().catch(() => this.playBeepSound());
            } catch (err) {
                this.playBeepSound();
            }
        }

        playBeepSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 800;
                osc.type = 'sine';
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.5);
                console.log('[NotificationSound] Beep played');
            } catch (err) {
                console.warn('[NotificationSound] Could not play beep:', err.message);
            }
        }

        showToast(title, message) {
            const isDarkMode = document.documentElement.classList.contains('dark');
            const toast = document.createElement('div');
            toast.className = `notification-toast ${isDarkMode ? 'dark-mode' : ''}`;
            toast.innerHTML = `
                <div class="notification-toast-title">${this.escapeHtml(title)}</div>
                <div class="notification-toast-message">${this.escapeHtml(message)}</div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        startPeriodicBadgeRefresh() {
            setInterval(() => {
                const models = ['pengajuan', 'pengajuan-perbaikan', 'pengajuan-dukungan'];
                models.forEach(model => this.refreshBadge(model));
            }, 15000);

            setTimeout(() => {
                const models = ['pengajuan', 'pengajuan-perbaikan', 'pengajuan-dukungan'];
                models.forEach(model => this.refreshBadge(model));
            }, 2000);
        }

        refreshBadge(model) {
            if (this.updateDebounce[model]) clearTimeout(this.updateDebounce[model]);
            this.updateDebounce[model] = setTimeout(() => {
                fetch(`/api/badge-count?model=${model}`)
                    .then(res => res.json())
                    .then(data => {
                        console.log(`[NotificationSound] Badge API for ${model}: ${data.count}`);
                    })
                    .catch(err => console.error('[NotificationSound] Badge error:', err));
            }, 200);
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.notificationHandler = new NotificationHandler();
        });
    } else {
        window.notificationHandler = new NotificationHandler();
    }

    document.addEventListener('livewire:navigated', () => {
        if (window.notificationHandler) {
            window.notificationHandler.setupEchoListener();
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => handleHighlightFromUrl());
    document.addEventListener('livewire:navigated', () => handleHighlightFromUrl());

    function handleHighlightFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const highlightId = params.get('highlight');
        if (!highlightId) return;

        const url = new URL(window.location);
        url.searchParams.delete('highlight');
        window.history.replaceState({}, '', url);

        const attempts = [200, 500, 1000, 1800, 3000];
        let found = false;

        attempts.forEach(delay => {
            setTimeout(() => {
                if (found) return;
                const rows = document.querySelectorAll('tr[wire\\:key]');
                rows.forEach(row => {
                    const key = row.getAttribute('wire:key') || '';
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
