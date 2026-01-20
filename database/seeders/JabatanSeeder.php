<?php

namespace Database\Seeders;

use App\Models\JabatanModel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JabatanModel::create([
            'nm_jabatan' => 'Kepala Bagian KOMLEK',
        ]);
        JabatanModel::create([
            'nm_jabatan' => 'Staff KOMLEK',
        ]);
        JabatanModel::create([
            'nm_jabatan' => 'Babu Kantor',
        ]);
    }
}
