@push('styles')
    @vite(['resources/css/app.css'])
    <style>
        /* Force remove padding for a truly flush table */
        .fi-section-content { padding: 0 !important; }
        .fi-section-header { border-bottom: 1px solid rgb(229, 231, 235) !important; margin-bottom: 0 !important; }
        .dark .fi-section-header { border-bottom-color: rgb(55, 65, 81) !important; }
    </style>
@endpush

<x-filament-panels::page>
    <div wire:poll.5s="checkNewRequest">
        @php
            $requests = $this->getRequests();
            $lastRecord = $requests->first();
            $lastUpdate = $lastRecord?->updated_at?->format('H:i:s') ?? now()->format('H:i:s');
        @endphp

        <x-filament::section shadow="none" class="border-gray-200 dark:border-gray-700">
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full py-1">
                    <div class="flex items-center gap-2">
                        <x-filament::icon
                            icon="heroicon-m-presentation-chart-bar"
                            class="h-5 w-5 text-primary-500"
                        />
                        <span class="font-bold tracking-tight">Monitoring Request Dukungan (Live)</span>
                    </div>
                    <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 flex items-center gap-1.5 uppercase tracking-widest">
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-full border border-gray-200 dark:border-gray-700">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-success-500"></span>
                            </span>
                            <span>Updated: {{ $lastUpdate }}</span>
                        </div>
                    </div>
                </div>
            </x-slot>

            <div class="overflow-x-auto border-none">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5 uppercase text-[10px] tracking-wider font-bold">
                            <th class="px-6 py-4 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">ID</th>
                            <th class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">No Nodis</th>
                            <th class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">Kegiatan</th>
                            <th class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">Ruangan</th>
                            <th class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">Tgl Kegiatan</th>
                            <th class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">Barang</th>
                            <th class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">Status</th>
                            <th class="px-6 py-3 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 text-right">Pemohon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $requests = $this->getRequests();
                        @endphp
                        @forelse ($requests as $request)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-bold text-primary-600 dark:text-primary-400">#{{ $request->id }}</td>
                                <td class="px-6 py-4 font-medium">{{ $request->nomor_nodis }}</td>
                                <td class="px-6 py-4">
                                    <div class="max-w-[250px] truncate" title="{{ $request->deskripsi_kegiatan }}">
                                        {{ $request->deskripsi_kegiatan }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $request->ruangan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                    {{ $request->tgl_kegiatan->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @if (is_array($request->req_barang) && count($request->req_barang) > 0)
                                            @foreach ($request->req_barang as $barang)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                                    {{ $barang['nama'] ?? '-' }} ({{ $barang['jumlah'] ?? 0 }})
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-xs text-gray-400 italic">No items</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-filament::badge :color="match ($request->status_dukungan) {
                                        'belum_didukung' => 'warning',
                                        'didukung' => 'success',
                                        'tidak_didukung' => 'danger',
                                    }" size="sm">
                                        {{ match ($request->status_dukungan) {
                                            'belum_didukung' => 'Belum Didukung',
                                            'didukung' => 'Didukung',
                                            'tidak_didukung' => 'Ditolak',
                                        } }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $request->pemohon?->name ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <x-filament::icon icon="heroicon-o-inbox" class="h-12 w-12 mb-2 opacity-20" />
                                        <span>Belum ada data request dukungan.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
