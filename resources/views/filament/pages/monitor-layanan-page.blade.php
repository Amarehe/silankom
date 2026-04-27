<x-filament-panels::page>
    {{-- CSS Kiosk Mode --}}
    <style>
        .kiosk-mode .fi-sidebar { display: none !important; }
        .kiosk-mode .fi-topbar { display: none !important; }
        .kiosk-mode .fi-main { padding: 1rem !important; margin: 0 !important; max-width: 100% !important; }
        .kiosk-mode .fi-header { display: none !important; }
    </style>

    <div x-data="{ kioskMode: false }">
        {{-- Tombol Toggle Kiosk Mode --}}
        <div class="flex justify-end mb-4">
            <x-filament::button 
                color="gray" 
                icon="heroicon-o-arrows-pointing-out"
                x-on:click="
                    kioskMode = !kioskMode; 
                    if(kioskMode) { 
                        document.body.classList.add('kiosk-mode'); 
                        document.documentElement.requestFullscreen().catch(e => {});
                    } else { 
                        document.body.classList.remove('kiosk-mode'); 
                        if(document.fullscreenElement) document.exitFullscreen();
                    }
                "
            >
                <span x-text="kioskMode ? 'Keluar Fullscreen' : 'Masuk Fullscreen (Kiosk Mode)'"></span>
            </x-filament::button>
        </div>

        <div wire:poll.10s class="w-full">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; align-items: start;">
                
                {{-- Kolom Peminjaman --}}
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-radius: 0.75rem; background-color: white; border-top: 4px solid #3b82f6; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" class="dark:bg-gray-900 dark:border-gray-800">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <x-filament::icon icon="heroicon-o-inbox-stack" class="w-6 h-6 text-primary-500" />
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #1f2937;" class="dark:text-white">Peminjaman</h2>
                        </div>
                        <x-filament::badge color="primary" size="lg">{{ $peminjamanBaru->count() }} Baru</x-filament::badge>
                    </div>

                    <div style="background-color: white; border-radius: 0.75rem; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" class="dark:bg-gray-900 dark:border-gray-800">
                        <table style="width: 100%; text-align: left; border-collapse: collapse;">
                            <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;" class="dark:bg-gray-800 dark:border-gray-700">
                                <tr>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap;" class="dark:text-gray-300">Pemohon</th>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap;" class="dark:text-gray-300">Barang (Unit)</th>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap; text-align: right;" class="dark:text-gray-300">Waktu Masuk</th>
                                </tr>
                            </thead>
                            <tbody style="divide-y: 1px solid #e5e7eb;" class="dark:divide-gray-800">
                                @forelse($peminjamanBaru as $item)
                                    <tr style="border-bottom: 1px solid #e5e7eb;" class="dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 500; color: #111827;" class="dark:text-white">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></div>
                                                {{ $item->user?->name ?? 'User' }}
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #4b5563;" class="dark:text-gray-400">
                                            {{ $item->kategori?->nama_kategori ?? '-' }} ({{ $item->jumlah }})
                                        </td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.75rem; color: #6b7280; text-align: right;" class="dark:text-gray-500">
                                            {{ $item->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 2rem 1rem; text-align: center; font-size: 0.875rem; color: #9ca3af;">
                                            Tidak ada peminjaman baru
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Perbaikan --}}
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-radius: 0.75rem; background-color: white; border-top: 4px solid #ef4444; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" class="dark:bg-gray-900 dark:border-gray-800">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <x-filament::icon icon="heroicon-o-wrench" class="w-6 h-6 text-danger-500" />
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #1f2937;" class="dark:text-white">Perbaikan</h2>
                        </div>
                        <x-filament::badge color="danger" size="lg">{{ $perbaikanBaru->count() }} Aktif</x-filament::badge>
                    </div>

                    <div style="background-color: white; border-radius: 0.75rem; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" class="dark:bg-gray-900 dark:border-gray-800">
                        <table style="width: 100%; text-align: left; border-collapse: collapse;">
                            <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;" class="dark:bg-gray-800 dark:border-gray-700">
                                <tr>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap;" class="dark:text-gray-300">Pemohon</th>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap;" class="dark:text-gray-300">Barang</th>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap; text-align: right;" class="dark:text-gray-300">Waktu Masuk</th>
                                </tr>
                            </thead>
                            <tbody style="divide-y: 1px solid #e5e7eb;" class="dark:divide-gray-800">
                                @forelse($perbaikanBaru as $item)
                                    <tr style="border-bottom: 1px solid #e5e7eb;" class="dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 500; color: #111827;" class="dark:text-white">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div class="w-2 h-2 rounded-full {{ $item->status_perbaikan === 'diajukan' ? 'bg-danger-500 animate-pulse' : 'bg-info-500' }}"></div>
                                                {{ $item->pemohon?->name ?? 'User' }}
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #4b5563;" class="dark:text-gray-400">
                                            {{ $item->nm_barang ?? '-' }}
                                        </td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.75rem; color: #6b7280; text-align: right;" class="dark:text-gray-500">
                                            {{ $item->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 2rem 1rem; text-align: center; font-size: 0.875rem; color: #9ca3af;">
                                            Tidak ada perbaikan aktif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Dukungan --}}
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-radius: 0.75rem; background-color: white; border-top: 4px solid #22c55e; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" class="dark:bg-gray-900 dark:border-gray-800">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <x-filament::icon icon="heroicon-o-hand-raised" class="w-6 h-6 text-success-500" />
                            <h2 style="font-size: 1.125rem; font-weight: 700; color: #1f2937;" class="dark:text-white">Dukungan</h2>
                        </div>
                        <x-filament::badge color="success" size="lg">{{ $dukunganBaru->count() }} Baru</x-filament::badge>
                    </div>

                    <div style="background-color: white; border-radius: 0.75rem; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);" class="dark:bg-gray-900 dark:border-gray-800">
                        <table style="width: 100%; text-align: left; border-collapse: collapse;">
                            <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;" class="dark:bg-gray-800 dark:border-gray-700">
                                <tr>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap;" class="dark:text-gray-300">Pemohon</th>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap;" class="dark:text-gray-300">Kegiatan</th>
                                    <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 600; color: #374151; white-space: nowrap; text-align: right;" class="dark:text-gray-300">Waktu Masuk</th>
                                </tr>
                            </thead>
                            <tbody style="divide-y: 1px solid #e5e7eb;" class="dark:divide-gray-800">
                                @forelse($dukunganBaru as $item)
                                    <tr style="border-bottom: 1px solid #e5e7eb;" class="dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; font-weight: 500; color: #111827;" class="dark:text-white">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div class="w-2 h-2 rounded-full bg-success-500 animate-pulse"></div>
                                                {{ $item->pemohon?->name ?? 'User' }}
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #4b5563;" class="dark:text-gray-400">
                                            {{ $item->nama_kegiatan ?? '-' }}
                                        </td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.75rem; color: #6b7280; text-align: right;" class="dark:text-gray-500">
                                            {{ $item->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 2rem 1rem; text-align: center; font-size: 0.875rem; color: #9ca3af;">
                                            Tidak ada dukungan baru
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-filament-panels::page>
