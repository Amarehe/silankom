<?php

namespace Database\Seeders;

use App\Models\MerekModel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MerekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MerekModel::create([
            'nama_merek' => 'Asus',
        ]);
        MerekModel::create([
            'nama_merek' => 'Dell',
        ]);
        MerekModel::create([
            'nama_merek' => 'Lenovo',
        ]);
        MerekModel::create([
            'nama_merek' => 'Acer',
        ]);
        MerekModel::create([
            'nama_merek' => 'HP',
        ]);
    }
}
