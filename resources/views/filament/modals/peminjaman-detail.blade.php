<div class="space-y-4">
    {{-- Header --}}
    <div class="border-b pb-3">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Detail Peminjaman Aktif
            </h3>
            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold
                @if($record->status_peminjaman === 'dipinjam') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                @elseif($record->status_peminjaman === 'dikembalikan') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                @endif">
                {{ ucfirst($record->status_peminjaman) }}
            </span>
        </div>
        <div class="mt-2 flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
            <span class="font-mono">{{ $record->nomor_surat }}</span>
            <span>•</span>
            <span>{{ $record->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

        {{-- DATA PEMINJAM --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                Data Peminjam
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Nama Lengkap</div>
                    <div class="mt-0.5 font-semibold text-gray-900 dark:text-white">
                        {{ $record->reqPinjam->user->name ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">NIP</div>
                    <div class="mt-0.5 font-mono font-semibold text-gray-900 dark:text-white">
                        {{ $record->reqPinjam->user->nip ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Unit Kerja</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ $record->reqPinjam->user->unitkerja->nm_unitkerja ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Jabatan</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ $record->reqPinjam->user->jabatan->nm_jabatan ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- DATA BARANG --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                Data Barang
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Nama Barang</div>
                    <div class="mt-0.5 font-semibold text-gray-900 dark:text-white">
                        {{ $record->barang->nama_barang ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Merek</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ $record->barang->merek->nama_merek ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Kategori</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ $record->barang->kategori->nama_kategori ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Nomor Seri</div>
                    <div class="mt-0.5 font-mono text-gray-900 dark:text-white">
                        {{ $record->barang->nomor_seri ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Peminjaman --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                Informasi Peminjaman
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tanggal Pinjam</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($record->tanggal_serah_terima)->format('d F Y') }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Kondisi Barang Saat Dipinjam</div>
                    <div class="mt-0.5">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold
                            @if($record->kondisi_barang === 'baik') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @elseif($record->kondisi_barang === 'rusak ringan') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                            @elseif($record->kondisi_barang === 'rusak berat') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                            @endif">
                            {{ ucfirst($record->kondisi_barang) }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Diproses Oleh</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ $record->admin->name ?? 'Sistem' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Pengembalian --}}
        @if($record->status_peminjaman === 'dikembalikan')
        <div class="rounded-lg border-2 border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-green-800 dark:text-green-300">
                Data Pengembalian
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-green-700 dark:text-green-400">Tanggal Kembali</div>
                    <div class="mt-0.5 text-green-900 dark:text-green-100">
                        {{ $record->tanggal_kembali ? \Carbon\Carbon::parse($record->tanggal_kembali)->format('d F Y') : '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-green-700 dark:text-green-400">Kondisi Saat Dikembalikan</div>
                    <div class="mt-0.5">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold
                            @if($record->kondisi_kembali === 'baik') bg-green-200 text-green-900 dark:bg-green-900/50 dark:text-green-200
                            @elseif($record->kondisi_kembali === 'rusak ringan') bg-yellow-200 text-yellow-900 dark:bg-yellow-900/50 dark:text-yellow-200
                            @elseif($record->kondisi_kembali === 'rusak berat') bg-red-200 text-red-900 dark:bg-red-900/50 dark:text-red-200
                            @else bg-gray-200 text-gray-900 dark:bg-gray-900/50 dark:text-gray-200
                            @endif">
                            {{ $record->kondisi_kembali ? ucfirst($record->kondisi_kembali) : '-' }}
                        </span>
                    </div>
                </div>
                @if($record->catatan_pengembalian)
                <div>
                    <div class="text-xs text-green-700 dark:text-green-400">Catatan Pengembalian</div>
                    <div class="mt-0.5 text-sm text-green-900 dark:text-green-100">
                        {!! nl2br(e($record->catatan_pengembalian)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-blue-800 dark:text-blue-300">
                Status Peminjaman
            </h4>
            <div class="flex items-center gap-3">
                <svg class="h-12 w-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <div class="text-sm font-semibold text-blue-900 dark:text-blue-100">Sedang Dipinjam</div>
                    <div class="text-xs text-blue-700 dark:text-blue-300">Barang belum dikembalikan</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Keperluan / Keterangan Peminjaman --}}
    @if($record->reqPinjam && $record->reqPinjam->keterangan)
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
        <h4 class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
            Keperluan Peminjaman
        </h4>
        <div class="text-sm text-gray-900 dark:text-white">
            {!! nl2br(e($record->reqPinjam->keterangan)) !!}
        </div>
    </div>
    @endif
</div>