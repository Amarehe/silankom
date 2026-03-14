<?php

namespace Database\Seeders;

use App\Models\PeminjamanModel;
use App\Services\NomorSuratService;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil pengajuan yang disetujui
        $reqPinjam = \App\Models\ReqPinjamModel::where('status', 'disetujui')->first();

        // Ambil barang yang tersedia
        $barang = \App\Models\BarangModel::where('status', 'tersedia')->first();

        // Ambil admin (role_id 1 atau 2)
        $admin = \App\Models\User::whereIn('role_id', [1, 2])->first();

        if (! $reqPinjam) {
            echo "⚠️ Warning: Tidak ada pengajuan yang disetujui. Skip seeding Peminjaman.\n";

            return;
        }

        if (! $barang) {
            echo "⚠️ Warning: Tidak ada barang tersedia. Skip seeding Peminjaman.\n";

            return;
        }

        if (! $admin) {
            echo "⚠️ Warning: Tidak ada user admin. Skip seeding Peminjaman.\n";

            return;
        }

        // Generate nomor surat menggunakan service
        $nomorSurat = NomorSuratService::generate();

        // Peminjaman yang masih dipinjam
        $peminjaman = PeminjamanModel::create([
            'nomor_surat' => $nomorSurat,
            'req_pinjam_id' => $reqPinjam->id,
            'barang_id' => $barang->id_barang,
            'admin_id' => $admin->id,
            'tanggal_serah_terima' => now()->subDays(3),
            'kondisi_barang' => 'baik',
            'kelengkapan' => 'Kabel power, kabel USB',
            'catatan_admin' => 'Harap dijaga dengan baik',
            'status_peminjaman' => 'dipinjam',
        ]);

        // Update status barang menjadi dipinjam
        $barang->update(['status' => 'dipinjam']);

        echo "✓ Peminjaman berhasil dibuat dengan nomor surat: {$nomorSurat}\n";

        // Buat peminjaman yang sudah dikembalikan (untuk testing)
        $barang2 = \App\Models\BarangModel::where('status', 'tersedia')
            ->where('id_barang', '!=', $barang->id_barang)
            ->first();

        if ($barang2) {
            // Buat req_pinjam baru untuk peminjaman kedua
            $karyawan = \App\Models\User::where('role_id', 4)->first();
            $kategori = \App\Models\KategoriModel::first();

            if ($karyawan && $kategori) {
                $reqPinjam2 = \App\Models\ReqPinjamModel::create([
                    'user_id' => $karyawan->id,
                    'kategori_id' => $kategori->id,
                    'jumlah' => 1,
                    'keterangan' => 'Untuk testing riwayat pengembalian',
                    'status' => 'disetujui',
                    'tanggal_request' => now()->subDays(10),
                ]);

                $nomorSurat2 = NomorSuratService::generate();
                $nomorSuratPengembalian = NomorSuratService::generatePengembalian();

                $peminjaman2 = PeminjamanModel::create([
                    'nomor_surat' => $nomorSurat2,
                    'nomor_surat_pengembalian' => $nomorSuratPengembalian,
                    'req_pinjam_id' => $reqPinjam2->id,
                    'barang_id' => $barang2->id_barang,
                    'admin_id' => $admin->id,
                    'admin_penerima_id' => $admin->id,
                    'tanggal_serah_terima' => now()->subDays(8),
                    'kondisi_barang' => 'baik',
                    'kelengkapan' => 'Lengkap',
                    'catatan_admin' => 'Peminjaman untuk testing',
                    'status_peminjaman' => 'dikembalikan',
                    'tanggal_kembali' => now()->subDays(2),
                    'kondisi_kembali' => 'baik',
                    'catatan_pengembalian' => 'Barang dikembalikan dalam kondisi baik',
                ]);

                echo "✓ Peminjaman yang sudah dikembalikan berhasil dibuat\n";
                echo "  - Nomor surat peminjaman: {$nomorSurat2}\n";
                echo "  - Nomor surat pengembalian: {$nomorSuratPengembalian}\n";
            }
        }
    }
}
