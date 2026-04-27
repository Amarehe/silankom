<x-filament-panels::page>
    <form wire:submit="tampilkanPreview">
        {{ $this->form }}
        <div class="flex flex-wrap items-center gap-3 mt-6" style="margin-top: 1.5rem;">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass" color="primary" size="lg">Tampilkan Data</x-filament::button>
            @if($showPreview)
                <x-filament::button wire:click="exportPdf" type="button" icon="heroicon-o-document-arrow-down" color="danger" size="lg">Export PDF</x-filament::button>
                <x-filament::button wire:click="exportExcel" type="button" icon="heroicon-o-table-cells" color="success" size="lg">Export Excel</x-filament::button>
                <x-filament::button tag="a" href="{{ $this->getExportUrl('print') }}" target="_blank" icon="heroicon-o-printer" color="info" size="lg">Print</x-filament::button>
            @endif
        </div>
    </form>

    @if(!empty($statistik) && $showPreview)
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                📊 Ringkasan Statistik — {{ $periodeLabel }}
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; width: 100%;">
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Dukungan</span>
                        <div class="text-3xl font-semibold tracking-tight text-primary-600 dark:text-primary-400">{{ $statistik['total'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Didukung</span>
                        <div class="text-3xl font-semibold tracking-tight text-success-600 dark:text-success-400">{{ $statistik['didukung'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum Didukung</span>
                        <div class="text-3xl font-semibold tracking-tight text-warning-600 dark:text-warning-400">{{ $statistik['belum_didukung'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak Didukung</span>
                        <div class="text-3xl font-semibold tracking-tight text-danger-600 dark:text-danger-400">{{ $statistik['tidak_didukung'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showPreview)
        @php $previewItems = $this->previewData; @endphp
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                📋 Preview Data <span class="text-sm font-normal text-gray-500">({{ $previewItems ? $previewItems->count() : 0 }} record)</span>
            </h3>
            <div class="fi-ta-content divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <table class="fi-ta-table w-full text-left divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">No</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">No Nodis</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Nama Kegiatan</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Pemohon</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Unit Kerja</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Ruangan</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Tgl Kegiatan</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Barang Diminta</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Status</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">PIC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @forelse($previewItems as $index => $item)
                            <tr class="fi-ta-row transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $index + 1 }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm font-medium text-primary-600 dark:text-primary-400" style="color: #2563eb;">{{ $item->nomor_nodis ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->nama_kegiatan ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->pemohon?->name ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->pemohon?->unitkerja?->nm_unitkerja ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->ruangan ?? '-' }}</div></td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->tgl_kegiatan ? \Carbon\Carbon::parse($item->tgl_kegiatan)->translatedFormat('d/m/Y') : '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm text-gray-500 whitespace-normal min-w-[200px]">
                                        @if(is_array($item->req_barang))
                                            @foreach($item->req_barang as $rb)
                                                <x-filament::badge color="gray" class="mb-1 inline-flex">{{ ($rb['nama'] ?? $rb['barang'] ?? '-') }} ({{ $rb['jumlah'] ?? '-' }})</x-filament::badge>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;">
                                    <div>
                                        <x-filament::badge color="{{ match($item->status_dukungan) { 'belum_didukung' => 'warning', 'didukung' => 'success', 'tidak_didukung' => 'danger', default => 'gray' } }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status_dukungan)) }}
                                        </x-filament::badge>
                                    </div>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->picDukungan?->name ?? '-' }}</div></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="fi-ta-cell p-0">
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
                    <div class="text-6xl mb-4">🤝</div>
                    <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-2">Pilih Periode Rekap</h3>
                    <p class="text-sm text-gray-400 dark:text-gray-500 max-w-md">Atur filter di atas lalu klik <strong>Tampilkan Data</strong> untuk preview. Setelah itu export ke PDF, Excel, atau print langsung.</p>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
