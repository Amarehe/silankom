<?php

namespace Database\Seeders;

use App\Models\KategoriModel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KategoriModel::create([
            'nama_kategori' => 'Laptop',
        ]);
        KategoriModel::create([
            'nama_kategori' => 'PC',
        ]);
        KategoriModel::create([
            'nama_kategori' => 'Printer',
        ]);
        KategoriModel::create([
            'nama_kategori' => 'Scanner',
        ]);
    }
}
