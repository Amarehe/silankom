<x-filament-panels::page>
    {{-- Filter Form --}}
    <form wire:submit="tampilkanPreview">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-3 mt-8">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass" color="primary" size="lg">
                Tampilkan Data
            </x-filament::button>

            @if($showPreview)
                <x-filament::button wire:click="exportPdf" type="button" icon="heroicon-o-document-arrow-down"
                    color="danger" size="lg">
                    📄 Export PDF
                </x-filament::button>

                <x-filament::button wire:click="exportExcel" type="button" icon="heroicon-o-table-cells"
                    color="success" size="lg">
                    📊 Export Excel
                </x-filament::button>

                <x-filament::button tag="a" href="{{ $this->getExportUrl('print') }}" target="_blank" color="info" size="lg" icon="heroicon-o-printer">
                    🖨️ Print
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
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Peminjaman</span>
                        <div class="text-3xl font-semibold tracking-tight text-primary-600 dark:text-primary-400" style="color: #2563eb;">{{ $statistik['total'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Sedang Dipinjam</span>
                        <div class="text-3xl font-semibold tracking-tight text-info-600 dark:text-info-400" style="color: #0ea5e9;">{{ $statistik['dipinjam'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Dikembalikan</span>
                        <div class="text-3xl font-semibold tracking-tight text-success-600 dark:text-success-400" style="color: #16a34a;">{{ $statistik['dikembalikan'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; width: 100%;">
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Kondisi Baik</span>
                        <div class="text-3xl font-semibold tracking-tight text-success-600 dark:text-success-400" style="color: #16a34a;">{{ $statistik['kondisi_baik'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rusak Ringan</span>
                        <div class="text-3xl font-semibold tracking-tight text-warning-600 dark:text-warning-400" style="color: #eab308;">{{ $statistik['kondisi_rusak_ringan'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rusak Berat</span>
                        <div class="text-3xl font-semibold tracking-tight text-danger-600 dark:text-danger-400" style="color: #dc2626;">{{ $statistik['kondisi_rusak_berat'] ?? 0 }}</div>
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
                <table class="fi-ta-table w-full text-left divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">No</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Nomor Surat</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Peminjam</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">NIP</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Unit Kerja</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Barang</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white" style="padding: 12px 16px; white-space: nowrap;">Kategori</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Tgl Pinjam</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Kondisi</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Tgl Kembali</th>
                            <th class="fi-ta-header-cell text-sm font-semibold text-gray-950 dark:text-white text-center" style="padding: 12px 16px; white-space: nowrap;">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @forelse($previewItems as $index => $item)
                            <tr class="fi-ta-row transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm text-gray-950 dark:text-white">{{ $index + 1 }}</div>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;">
                                    <div class="text-sm font-medium text-primary-600 dark:text-primary-400" style="color: #2563eb;">{{ $item->nomor_surat ?? '-' }}</div>
                                </td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->reqPinjam?->user?->name ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->reqPinjam?->user?->nip ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->reqPinjam?->user?->unitkerja?->nm_unitkerja ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->barang?->nama_barang ?? '-' }}</div></td>
                                <td class="fi-ta-cell" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->barang?->kategori?->nama_kategori ?? '-' }}</div></td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->tanggal_serah_terima ? \Carbon\Carbon::parse($item->tanggal_serah_terima)->translatedFormat('d/m/Y') : '-' }}</div></td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;">
                                    <div>
                                        <x-filament::badge color="{{ match($item->kondisi_barang) { 'baik' => 'success', 'rusak ringan' => 'warning', 'rusak berat' => 'danger', default => 'gray' } }}">
                                            {{ ucfirst($item->kondisi_barang) }}
                                        </x-filament::badge>
                                    </div>
                                </td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;"><div class="text-sm text-gray-950 dark:text-white">{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('d/m/Y') : '-' }}</div></td>
                                <td class="fi-ta-cell text-center" style="padding: 12px 16px; white-space: nowrap;">
                                    <div>
                                        <x-filament::badge color="{{ $item->status_peminjaman === 'dikembalikan' ? 'success' : 'info' }}">
                                            {{ ucfirst($item->status_peminjaman) }}
                                        </x-filament::badge>
                                    </div>
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
                    <div class="text-6xl mb-4">📋</div>
                    <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-2">Pilih Periode Laporan</h3>
                    <p class="text-sm text-gray-400 dark:text-gray-500 max-w-md">
                        Atur filter periode dan kriteria audit di atas, lalu klik <strong>Tampilkan Data</strong> untuk preview.
                        Setelah itu Anda dapat export ke PDF, Excel, atau print langsung.
                    </p>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
