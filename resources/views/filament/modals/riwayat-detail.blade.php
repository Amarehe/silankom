<div class="space-y-4">
    {{-- Header --}}
    <div class="border-b pb-3">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Detail Riwayat Peminjaman
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

    {{-- Timeline Peminjaman --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Data Peminjaman --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <h4 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Data Peminjaman
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tanggal Pinjam</div>
                    <div class="mt-0.5 font-semibold text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($record->tanggal_serah_terima)->format('d F Y') }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Kondisi Saat Dipinjam</div>
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

        {{-- Data Pengembalian --}}
        @if($record->status_peminjaman === 'dikembalikan')
        <div class="rounded-lg border-2 border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <h4 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-green-800 dark:text-green-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Data Pengembalian
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-green-700 dark:text-green-400">Tanggal Kembali</div>
                    <div class="mt-0.5 font-semibold text-green-900 dark:text-green-100">
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
            <h4 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-800 dark:text-blue-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Status
            </h4>
            <div class="flex items-center gap-3">
                <div>
                    <div class="text-sm font-semibold text-blue-900 dark:text-blue-100">Masih Dipinjam</div>
                    <div class="text-xs text-blue-700 dark:text-blue-300">Barang belum dikembalikan</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Durasi Peminjaman --}}
    @if($record->status_peminjaman === 'dikembalikan' && $record->tanggal_kembali)
    <div class="rounded-lg border border-gray-200 bg-gradient-to-r from-blue-50 to-green-50 p-4 dark:border-gray-700 dark:from-blue-900/10 dark:to-green-900/10">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300">Durasi Peminjaman</div>
                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ \Carbon\Carbon::parse($record->tanggal_serah_terima)->diffInDays(\Carbon\Carbon::parse($record->tanggal_kembali)) }}
                    <span class="text-base font-normal text-gray-600 dark:text-gray-400">hari</span>
                </div>
            </div>
            <svg class="h-16 w-16 text-blue-200 dark:text-blue-800" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
            </svg>
        </div>
    </div>
    @endif

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