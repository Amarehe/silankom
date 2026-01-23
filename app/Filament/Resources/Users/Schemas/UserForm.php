<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->label('NIP')
                    ->maxLength(30)
                    // PENTING: Agar saat edit NIP yang sama tidak dianggap duplikat
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->validationMessages([
                        'required' => 'NIP wajib diisi!!',
                        'unique' => 'NIP sudah terdaftar!!',
                    ])
                    ->placeholder('Masukkan NIP'),
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->rule('required')
                    ->validationMessages([
                        'required' => 'Nama User wajib diisi!!',
                    ])
                    ->placeholder('Masukkan Nama Lengkap User'),

                Select::make('jabatan_id')
                    ->searchable()
                    ->preload()
                    ->relationship('jabatan', 'nm_jabatan')
                    ->label('Jabatan')
                    ->rule('required')
                    ->validationMessages([
                        'required' => 'Jabatan wajib diisi!!',
                    ])
                    ->placeholder('Pilih Jabatan'),

                Select::make('unitkerja_id')
                    ->searchable()
                    ->preload()
                    ->relationship('unitkerja', 'nm_unitkerja')
                    ->label('Unit Kerja')
                    ->rule('required')
                    ->validationMessages([
                        'required' => 'Unit Kerja wajib diisi!!',
                    ])
                    ->placeholder('Pilih Unit Kerja'),

                Select::make('role_id')
                    ->searchable()
                    ->preload()
                    ->relationship('role', 'nm_role')
                    ->label('Role User')
                    ->rule('required')
                    ->validationMessages([
                        'required' => 'Role User wajib diisi!!',
                    ])
                    ->placeholder('Pilih Role User'),

                TextInput::make('password')
                    ->revealable()
                    ->password()
                    // 1. Wajib hanya saat CREATE (Buat Baru)
                    ->required(fn(string $operation): bool => $operation === 'create')

                    // 2. Hanya simpan ke DB jika form diisi (filled)
                    // Jika kosong saat edit, password lama tidak akan tertimpa
                    ->dehydrated(fn(?string $state) => filled($state))

                    // 3. Otomatis Hash password sebelum simpan
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state)),

            ])->columns(2) // Membagi form jadi 2 kolom agar rapi
        ;
    }
}
