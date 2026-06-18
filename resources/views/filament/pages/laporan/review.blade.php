<x-filament-panels::page>
    {{-- Filter Form --}}
    <form wire:submit="tampilkanPreview">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-3 mt-6" style="margin-top: 1.5rem;">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass" color="primary" size="lg">
                Tampilkan Data
            </x-filament::button>

            @if($showPreview)
                <x-filament::button wire:click="exportPdf" type="button" icon="heroicon-o-document-arrow-down"
                    color="danger" size="lg">
                    Export PDF
                </x-filament::button>

                <x-filament::button wire:click="exportExcel" type="button" icon="heroicon-o-table-cells"
                    color="success" size="lg">
                    Export Excel
                </x-filament::button>

                <x-filament::button tag="a" href="{{ $this->getExportUrl('print') }}" target="_blank" color="info" size="lg" icon="heroicon-o-printer">
                    Print
                </x-filament::button>
            @endif
        </div>
    </form>

    {{-- Statistik --}}
    @if(!empty($statistik) && $showPreview)
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                📊 Ringkasan Statistik — {{ $periodeLabel }}
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; width: 100%; margin-bottom: 1rem;">
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Review</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #2563eb;">{{ $statistik['total'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">⭐ Rata-rata Rating</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #eab308;">{{ $statistik['avg_rating'] ?? 0 }}/5</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">⏱️ Rata-rata Kecepatan</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #0ea5e9;">{{ $statistik['avg_kecepatan'] ?? 0 }}/5</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">✅ Rata-rata Kualitas</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #16a34a;">{{ $statistik['avg_kualitas'] ?? 0 }}/5</div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; width: 100%;">
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">🤝 Rata-rata Pelayanan</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #8b5cf6;">{{ $statistik['avg_pelayanan'] ?? 0 }}/5</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">📦 Review Peminjaman</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #0ea5e9;">{{ $statistik['peminjaman'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">🔧 Review Perbaikan</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #eab308;">{{ $statistik['perbaikan'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">🤝 Review Dukungan</span>
                        <div class="text-3xl font-semibold tracking-tight" style="color: #2563eb;">{{ $statistik['dukungan'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Preview Table --}}
    @if($showPreview)
        @php $previewItems = $this->previewData; @endphp
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                📋 Preview Data <span class="text-sm font-normal text-gray-500">({{ $previewItems ? $previewItems->count() : 0 }} record)</span>
            </h3>
            
            <div class="fi-ta-content divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <table class="fi-ta-table w-full min-w-max text-left divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">No</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Karyawan</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">NIP</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Unit Kerja</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Jenis</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Ditangani Oleh</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Rating</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Kecepatan</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Kualitas</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Pelayanan</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Komentar</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Tgl Review</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @forelse($previewItems as $index => $item)
                            @php
                                $jenisLayanan = match($item->reviewable_type) {
                                    \App\Models\PeminjamanModel::class => 'Peminjaman',
                                    \App\Models\PerbaikanModel::class => 'Perbaikan',
                                    \App\Models\ReqDukunganModel::class => 'Dukungan',
                                    default => '-',
                                };
                                $jenisColor = match($item->reviewable_type) {
                                    \App\Models\PeminjamanModel::class => 'info',
                                    \App\Models\PerbaikanModel::class => 'warning',
                                    \App\Models\ReqDukunganModel::class => 'primary',
                                    default => 'gray',
                                };
                                $picName = match($item->reviewable_type) {
                                    \App\Models\PeminjamanModel::class => $item->reviewable?->admin?->name ?? '-',
                                    \App\Models\PerbaikanModel::class => $item->reviewable?->teknisi?->name ?? '-',
                                    \App\Models\ReqDukunganModel::class => $item->reviewable?->picDukungan?->name ?? '-',
                                    default => '-',
                                };
                            @endphp
                            <tr class="fi-ta-row transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $index + 1 }}</div>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm font-medium text-gray-950 dark:text-white">{{ $item->user?->name ?? '-' }}</div>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $item->user?->nip ?? '-' }}</div>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $item->user?->unitkerja?->nm_unitkerja ?? '-' }}</div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px;">
                                    <x-filament::badge :color="$jenisColor">
                                        {{ $jenisLayanan }}
                                    </x-filament::badge>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm font-medium text-gray-950 dark:text-white">{{ $picName }}</div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px;">
                                    <div class="text-sm font-bold text-yellow-500">{{ str_repeat('⭐', $item->rating) }}</div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $item->aspek_kecepatan }}/5</div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $item->aspek_kualitas }}/5</div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $item->aspek_pelayanan }}/5</div>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; max-width: 200px;">
                                    <div class="text-sm text-gray-950 dark:text-white truncate">{{ $item->komentar ?? '-' }}</div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $item->created_at?->translatedFormat('d/m/Y') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="fi-ta-cell p-0">
                                    <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500 dark:text-gray-400">
                                        <span class="text-base font-medium">Tidak ada data ditemukan</span>
                                        <span class="text-sm mt-1">Coba sesuaikan filter pencarian di atas.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="mt-6">
            <x-filament::section>
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="text-6xl mb-4">⭐</div>
                    <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-2">Pilih Periode Rekap Review</h3>
                    <p class="text-sm text-gray-400 dark:text-gray-500 max-w-md">
                        Atur filter periode dan kriteria di atas, lalu klik <strong>Tampilkan Data</strong> untuk preview.
                        Setelah itu Anda dapat export ke PDF, Excel, atau print langsung.
                    </p>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
