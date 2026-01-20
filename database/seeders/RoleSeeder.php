<?php

namespace Database\Seeders;

use App\Models\RoleModel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoleModel::create([
            'nm_role' => 'Super Admin',
        ]);
        RoleModel::create([
            'nm_role' => 'Admin Komlek',
        ]);
        RoleModel::create([
            'nm_role' => 'Teknisi Komlek',
        ]);
        RoleModel::create([
            'nm_role' => 'Karyawan',
        ]);
    }
}
