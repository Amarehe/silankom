<?php

namespace Database\Seeders;

use App\Models\UnitKerjaModel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnitKerjaModel::create([
            'nm_unitkerja' => 'Biro Telematika',
        ]);
        UnitKerjaModel::create([
            'nm_unitkerja' => 'SDM',
        ]);
        UnitKerjaModel::create([
            'nm_unitkerja' => 'Bagian Keuangan',
        ]);
    }
}
