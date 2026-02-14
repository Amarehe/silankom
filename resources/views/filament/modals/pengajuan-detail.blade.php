<div class="space-y-4">
    {{-- Header --}}
    <div class="border-b pb-3">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Detail Pengajuan Peminjaman
            </h3>
            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold
                @if($record->status === 'diproses') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                @elseif($record->status === 'disetujui') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @elseif($record->status === 'ditolak') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                @endif">
                {{ ucfirst($record->status) }}
            </span>
        </div>
        <div class="mt-2 flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
            <span class="font-mono">#{{ str_pad($record->id, 4, '0', STR_PAD_LEFT) }}</span>
            <span>•</span>
            <span>{{ $record->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        
        {{-- DATA PEMOHON --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                Data Pemohon
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Nama Lengkap</div>
                    <div class="mt-0.5 font-semibold text-gray-900 dark:text-white">
                        {{ $record->user->name ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">NIP</div>
                    <div class="mt-0.5 font-mono font-semibold text-gray-900 dark:text-white">
                        {{ $record->user->nip ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Unit Kerja</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ $record->user->unitkerja->nm_unitkerja ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Jabatan</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ $record->user->jabatan->nama_jabatan ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- RINCIAN PENGAJUAN --}}
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                Rincian Pengajuan
            </h4>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Kategori Barang</div>
                    <div class="mt-0.5 font-semibold text-gray-900 dark:text-white">
                        {{ $record->kategori->nama_kategori ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Jumlah Permintaan</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $record->jumlah }}</span>
                        <span class="ml-1 text-sm">unit</span>
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Tanggal Pengajuan</div>
                    <div class="mt-0.5 text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($record->tanggal_request)->format('d F Y') }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Status Pengajuan</div>
                    <div class="mt-0.5 font-semibold
                        @if($record->status === 'diproses') text-yellow-600 dark:text-yellow-400
                        @elseif($record->status === 'disetujui') text-green-600 dark:text-green-400
                        @elseif($record->status === 'ditolak') text-red-600 dark:text-red-400
                        @else text-gray-600
                        @endif">
                        {{ ucfirst($record->status) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Keterangan --}}
    @if($record->keterangan)
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
        <h4 class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
            Keperluan / Keterangan
        </h4>
        <div class="text-sm text-gray-900 dark:text-white">
            {!! nl2br(e($record->keterangan)) !!}
        </div>
    </div>
    @endif

    {{-- Alasan Penolakan --}}
    @if($record->status === 'ditolak' && $record->alasan_penolakan)
    <div class="rounded-lg border-2 border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
        <h4 class="mb-2 text-xs font-bold uppercase tracking-wider text-red-800 dark:text-red-300">
            Alasan Penolakan
        </h4>
        <div class="text-sm font-medium text-red-700 dark:text-red-400">
            {!! nl2br(e($record->alasan_penolakan)) !!}
        </div>
    </div>
    @endif
</div>
