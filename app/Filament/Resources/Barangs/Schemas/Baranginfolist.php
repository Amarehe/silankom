<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class Baranginfolist
{

public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([
            // --- BAGIAN 1: INFORMASI UTAMA ---
            Section::make('Informasi Barang')
                ->icon('heroicon-m-cube')
                ->schema([
                    TextEntry::make('nama_barang')
                        ->label('Nama Barang')
                        ->weight(FontWeight::Bold)
                        ->size('lg')
                        ->columnSpanFull(), // Agar nama barang panjang ke samping

                    TextEntry::make('kategori.nama_kategori')
                        ->label('Kategori')
                        ->icon('heroicon-m-tag')
                        ->badge()
                        ->color('info'),

                    TextEntry::make('merek.nama_merek')
                        ->label('Merek')
                        ->icon('heroicon-m-check-badge')
                        ->placeholder('-'),

                    TextEntry::make('tahun')
                        ->label('Tahun Pengadaan')
                        ->icon('heroicon-m-calendar')
                        ->placeholder('-'),
                    
                    TextEntry::make('label')
                        ->label('Label/Tag')
                        ->icon('heroicon-m-bookmark')
                        ->placeholder('-'),
                ])
                ->columns(2),

            // --- BAGIAN 2: IDENTITAS & STATUS ---
            Section::make('Detail Status & Kondisi')
                ->icon('heroicon-m-clipboard-document-check')
                ->schema([
                    TextEntry::make('serial_number')
                        ->label('Serial Number (SN)')
                        ->icon('heroicon-m-qr-code')
                        ->fontFamily('mono') // Font ala koding
                        ->copyable() // Agar mudah dicopy admin
                        ->columnSpanFull(),

                    // --- LOGIKA KONDISI (Sesuai Tabel) ---
                    TextEntry::make('kondisi')
                        ->label('Kondisi Fisik')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'baik'            => 'success',
                            'perlu_perbaikan' => 'warning',
                            'rusak'           => 'danger',
                            default           => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'baik'            => 'Baik',
                            'perlu_perbaikan' => 'Perlu Perbaikan',
                            'rusak'           => 'Rusak',
                            default           => $state,
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'baik'            => 'heroicon-m-check-circle',
                            'perlu_perbaikan' => 'heroicon-m-wrench-screwdriver',
                            'rusak'           => 'heroicon-m-x-circle',
                            default           => 'heroicon-m-question-mark-circle',
                        }),

                    // --- LOGIKA STATUS (Sesuai Tabel) ---
                    TextEntry::make('status')
                        ->label('Status Peminjaman')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'tersedia' => 'success',
                            'dipinjam' => 'info',
                            default    => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'tersedia' => 'Tersedia',
                            'dipinjam' => 'Sedang Dipinjam',
                            default    => $state,
                        })
                         ->icon(fn (string $state): string => match ($state) {
                            'tersedia' => 'heroicon-m-archive-box',
                            'dipinjam' => 'heroicon-m-arrow-right-start-on-rectangle',
                            default    => 'heroicon-m-question-mark-circle',
                        }),
                ])
                ->columns(2),

            // --- BAGIAN 3: TIMESTAMP (Opsional tapi berguna) ---
            Section::make('Metadata')
                ->collapsed() // Default tertutup agar rapi
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Dibuat Pada')
                        ->dateTime('d M Y, H:i'),
                    TextEntry::make('updated_at')
                        ->label('Terakhir Diupdate')
                        ->dateTime('d M Y, H:i'),
                ])
                ->columns(2),
        ]);
}
}