<?php

namespace Database\Seeders;

use App\Models\BarangModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Laptop Asus
        BarangModel::create([
            'nama_barang' => 'Laptop Asus ROG Strix G15',
            'kategori_id' => 1, // Laptop
            'merek_id' => 1, // Asus
            'serial_number' => 'ASU-2024-001',
            'label' => 'INV-LP-001',
            'kondisi' => 'baik',
            'tahun' => 2024,
            'status' => 'tersedia',
            'keterangan' => 'Laptop gaming dengan spesifikasi tinggi',
            'user_id' => 1,
        ]);

        // Laptop Dell
        BarangModel::create([
            'nama_barang' => 'Laptop Dell Latitude 5420',
            'kategori_id' => 1, // Laptop
            'merek_id' => 2, // Dell
            'serial_number' => 'DELL-2023-002',
            'label' => 'INV-LP-002',
            'kondisi' => 'baik',
            'tahun' => 2023,
            'status' => 'dipinjam',
            'keterangan' => 'Laptop untuk keperluan administrasi',
            'user_id' => 1,
        ]);

        // PC Lenovo
        BarangModel::create([
            'nama_barang' => 'PC Lenovo ThinkCentre M720',
            'kategori_id' => 2, // PC
            'merek_id' => 3, // Lenovo
            'serial_number' => 'LEN-2023-003',
            'label' => 'INV-PC-001',
            'kondisi' => 'baik',
            'tahun' => 2023,
            'status' => 'tersedia',
            'keterangan' => 'PC desktop untuk kantor',
            'user_id' => 1,
        ]);

        // Printer HP
        BarangModel::create([
            'nama_barang' => 'Printer HP LaserJet Pro M404dn',
            'kategori_id' => 3, // Printer
            'merek_id' => 5, // HP
            'serial_number' => 'HP-2022-004',
            'label' => 'INV-PR-001',
            'kondisi' => 'perlu_perbaikan',
            'tahun' => 2022,
            'status' => 'tersedia',
            'keterangan' => 'Printer rusak pada bagian roller',
            'user_id' => 1,
        ]);

        // Scanner
        BarangModel::create([
            'nama_barang' => 'Scanner HP ScanJet Pro 2500',
            'kategori_id' => 4, // Scanner
            'merek_id' => 5, // HP
            'serial_number' => 'HP-2023-005',
            'label' => 'INV-SC-001',
            'kondisi' => 'baik',
            'tahun' => 2023,
            'status' => 'tersedia',
            'keterangan' => 'Scanner untuk dokumen',
            'user_id' => 1,
        ]);

        // Laptop Acer
        BarangModel::create([
            'nama_barang' => 'Laptop Acer Aspire 5',
            'kategori_id' => 1, // Laptop
            'merek_id' => 4, // Acer
            'serial_number' => 'ACR-2024-006',
            'label' => 'INV-LP-003',
            'kondisi' => 'baik',
            'tahun' => 2024,
            'status' => 'tersedia',
            'keterangan' => 'Laptop untuk keperluan umum',
            'user_id' => 1,
        ]);
    }
}
