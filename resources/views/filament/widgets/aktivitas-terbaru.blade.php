<x-filament-widgets::widget>
    <x-filament::section heading="Aktivitas Terbaru" icon="heroicon-o-bell-alert" wire:poll.15s>
        @php
            $aktivitas = $this->getAktivitas();
        @endphp

        @if($aktivitas->isEmpty())
            <div style="display: flex; align-items: center; justify-content: center; padding: 2rem 0; color: #9ca3af; font-size: 0.875rem;">
                Belum ada aktivitas
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 0;">
                @foreach($aktivitas as $index => $item)
                    @php
                        $colorMap = [
                            'warning' => ['bg' => '#fef3c7', 'text' => '#d97706', 'darkBg' => 'rgba(251,191,36,0.1)', 'darkText' => '#fbbf24'],
                            'success' => ['bg' => '#d1fae5', 'text' => '#059669', 'darkBg' => 'rgba(16,185,129,0.1)', 'darkText' => '#34d399'],
                            'danger'  => ['bg' => '#fee2e2', 'text' => '#dc2626', 'darkBg' => 'rgba(239,68,68,0.1)', 'darkText' => '#f87171'],
                            'info'    => ['bg' => '#dbeafe', 'text' => '#2563eb', 'darkBg' => 'rgba(59,130,246,0.1)', 'darkText' => '#60a5fa'],
                            'gray'    => ['bg' => '#f3f4f6', 'text' => '#6b7280', 'darkBg' => 'rgba(107,114,128,0.1)', 'darkText' => '#9ca3af'],
                        ];
                        $colors = $colorMap[$item['warna']] ?? $colorMap['gray'];

                        $modulColorMap = [
                            'Peminjaman' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'ring' => 'rgba(37,99,235,0.2)'],
                            'Perbaikan'  => ['bg' => '#fff7ed', 'text' => '#c2410c', 'ring' => 'rgba(234,88,12,0.2)'],
                            'Dukungan'   => ['bg' => '#faf5ff', 'text' => '#7c3aed', 'ring' => 'rgba(124,58,237,0.2)'],
                        ];
                        $modulColors = $modulColorMap[$item['modul']] ?? $modulColorMap['Peminjaman'];
                    @endphp

                    <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; {{ !$loop->last ? 'border-bottom: 1px solid #e5e7eb;' : '' }}">
                        {{-- Icon circle --}}
                        <div style="
                            width: 36px;
                            height: 36px;
                            min-width: 36px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            background-color: {{ $colors['bg'] }};
                            color: {{ $colors['text'] }};
                        ">
                            <x-filament::icon :icon="$item['ikon']" style="width: 18px; height: 18px;" />
                        </div>

                        {{-- Content --}}
                        <div style="flex: 1; min-width: 0;">
                            {{-- Title row --}}
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <span style="font-size: 0.875rem; font-weight: 500; color: #111827;">
                                    {{ $item['deskripsi'] }}
                                </span>
                                <span style="
                                    display: inline-flex;
                                    align-items: center;
                                    padding: 1px 8px;
                                    font-size: 0.6875rem;
                                    font-weight: 500;
                                    border-radius: 9999px;
                                    background-color: {{ $modulColors['bg'] }};
                                    color: {{ $modulColors['text'] }};
                                    border: 1px solid {{ $modulColors['ring'] }};
                                    white-space: nowrap;
                                ">
                                    {{ $item['modul'] }}
                                </span>
                            </div>

                            {{-- Meta row --}}
                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px; font-size: 0.75rem; color: #6b7280;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 12px; height: 12px; flex-shrink: 0;">
                                    <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                                </svg>
                                <span style="font-weight: 500;">{{ $item['user'] }}</span>
                                <span style="color: #d1d5db;">&bull;</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 12px; height: 12px; flex-shrink: 0;">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $item['waktu']->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
