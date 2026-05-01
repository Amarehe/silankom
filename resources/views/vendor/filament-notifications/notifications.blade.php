@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
@endphp

<div>
    <div
        @class([
            'fi-no',
            'fi-align-' . static::$alignment->value,
            'fi-vertical-align-' . static::$verticalAlignment->value,
        ])
        role="status"
    >
        @foreach ($notifications as $notification)
            {{ $notification }}
        @endforeach
    </div>

    @if ($broadcastChannel = $this->getBroadcastChannel())
        @script
            <script>
                window.addEventListener('EchoLoaded', () => {
                    // Listen untuk custom toast event (kompatibel dengan Reverb)
                    window.Echo.private(@js($broadcastChannel)).listen(
                        '.notification-toast.received',
                        (notification) => {
                            setTimeout(
                                () => {
                                    notification.format = 'filament';
                                    notification.duration = notification.duration || 8000;
                                    notification.actions = notification.actions || [];
                                    notification.viewData = notification.viewData || [];
                                    notification.id = notification.id || ('toast-' + Date.now());
                                    $wire.handleBroadcastNotification(notification);
                                },
                                500,
                            )
                        },
                    )
                })

                if (window.Echo) {
                    window.dispatchEvent(new CustomEvent('EchoLoaded'))
                }
            </script>
        @endscript
    @endif
</div>
