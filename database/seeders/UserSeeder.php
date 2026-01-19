<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nip' => '12345678',
            'name' => 'Quartiez',
            'password' => Hash::make('admin123'),
            'jabatan_id' => 1 ,
            'unitkerja_id' => 1 ,
            'role_id' => 1 ,
        ]);
    }
}
