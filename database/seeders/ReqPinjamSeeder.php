<?php

namespace Database\Seeders;

use App\Models\ReqPinjamModel;
use Illuminate\Database\Seeder;

class ReqPinjamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil atau buat user karyawan (role_id = 4)
        $karyawan = \App\Models\User::where('role_id', 4)->first();

        // Jika belum ada karyawan, buat user karyawan baru
        if (! $karyawan) {
            $jabatan = \App\Models\JabatanModel::first();
            $unitkerja = \App\Models\UnitKerjaModel::first();

            if (! $jabatan || ! $unitkerja) {
                echo "⚠️ Warning: Tidak ada jabatan atau unit kerja. Skip seeding ReqPinjam.\n";

                return;
            }

            $karyawan = \App\Models\User::create([
                'nip' => '87654321',
                'name' => 'Budi Santoso',
                'password' => \Illuminate\Support\Facades\Hash::make('karyawan123'),
                'jabatan_id' => $jabatan->id,
                'unitkerja_id' => $unitkerja->id,
                'role_id' => 4, // Role Karyawan
            ]);

            echo "✓ User karyawan berhasil dibuat (NIP: 87654321, Password: karyawan123)\n";
        }

        // Ambil kategori yang ada
        $kategoris = \App\Models\KategoriModel::limit(3)->get();

        if ($kategoris->count() < 1) {
            echo "⚠️ Warning: Tidak ada kategori. Skip seeding ReqPinjam.\n";

            return;
        }

        // Pengajuan 1: Diproses
        ReqPinjamModel::create([
            'user_id' => $karyawan->id,
            'kategori_id' => $kategoris[0]->id,
            'jumlah' => 1,
            'keterangan' => 'Untuk keperluan presentasi project',
            'status' => 'diproses',
            'tanggal_request' => now()->subDays(2),
        ]);

        // Pengajuan 2: Disetujui (jika ada kategori kedua)
        if ($kategoris->count() >= 2) {
            ReqPinjamModel::create([
                'user_id' => $karyawan->id,
                'kategori_id' => $kategoris[1]->id,
                'jumlah' => 1,
                'keterangan' => 'Untuk cetak dokumen laporan',
                'status' => 'disetujui',
                'tanggal_request' => now()->subDays(5),
            ]);
        }

        // Pengajuan 3: Ditolak (jika ada kategori ketiga)
        if ($kategoris->count() >= 3) {
            ReqPinjamModel::create([
                'user_id' => $karyawan->id,
                'kategori_id' => $kategoris[2]->id,
                'jumlah' => 1,
                'keterangan' => 'Untuk rapat tim',
                'status' => 'ditolak',
                'alasan_penolakan' => 'Barang sedang digunakan untuk acara lain',
                'tanggal_request' => now()->subDays(7),
            ]);
        }
    }
}
