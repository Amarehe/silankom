<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nip')
                    ->label('NIP')
                    ->icon('heroicon-m-identification')
                    ->copyable(),
                TextEntry::make('name')
                    ->label('Nama Lengkap')
                    ->weight('bold'),
                TextEntry::make('jabatan.nm_jabatan')
                    ->label('Jabatan')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-briefcase'),
                TextEntry::make('unitkerja.nm_unitkerja')
                    ->label('Unit Kerja')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-building-office'),
                TextEntry::make('role.nm_role')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Super Admin'    => 'danger',  // Merah (Level Tertinggi/Kritis)
                        'Admin Komlek'   => 'warning', // Kuning/Oranye (Admin Spesifik)
                        'Teknisi Komlek' => 'info',    // Biru (Warna umum untuk teknis/info)
                        'Karyawan'       => 'success', // Hijau (User Standar/Aman)
                        default          => 'gray',    // Abu-abu jika ada role lain
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Super Admin'    => 'heroicon-m-shield-check',       // Ikon Perisai
                        'Admin Komlek'   => 'heroicon-m-computer-desktop',   // Ikon Komputer
                        'Teknisi Komlek' => 'heroicon-m-wrench-screwdriver', // Ikon Obeng/Bengkel
                        'Karyawan'       => 'heroicon-m-user',               // Ikon Orang biasa
                        default          => 'heroicon-m-question-mark-circle',
                    }),
                TextEntry::make('last_login')
                    ->label('Terakhir Login')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->since(),
                // TextEntry::make('email')
                //     ->label('Email address')
                //     ->placeholder('-'),
                // TextEntry::make('email_verified_at')
                //     ->dateTime('d M Y, H:i')
                //     ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-'),
            ]);
    }
}
