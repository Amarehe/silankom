<?php

namespace App\Filament\Resources\Jabatans\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use RealRashid\SweetAlert\Facades\Alert;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Jabatans\JabatanResource;
use Filament\Support\Icons\Heroicon;

class ListJabatans extends ListRecords
{
    protected static string $resource = JabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->button()
                ->icon('heroicon-o-plus')
                ->color('info')
                ->label('Tambah Data') // Opsional: Mengganti label tombol
                ->modalHeading('Tambah Jabatan Baru') // Opsional: Judul Modal
                ->modalWidth('xl') // Pilihan: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl', 'full'
                ->createAnother(true)
                ->successNotificationTitle('Data Jabatan berhasil disimpan')
                // 1. Kustomisasi Tombol "Create" (Simpan)
                ->modalSubmitAction(
                    fn(Action $action) =>
                    $action->label('Simpan Data') // Ganti Tulisan
                        ->color('success')     // Ganti Warna (success, danger, info, warning)
                        ->icon(Heroicon::CheckCircle) // Tambah Icon (Opsional)
                )

                // 2. Kustomisasi Tombol "Cancel" (Batal)
                ->modalCancelAction(
                    fn(Action $action) =>
                    $action->label('Batal')    // Ganti Tulisan
                        ->color('danger') 
                        ->icon(Heroicon::XCircle)
                        
                ),

            // // 3. Eksekusi SweetAlert setelah data tersimpan
            // ->after(function () {
            //     Alert::success('Berhasil', 'Data Jabatan berhasil ditambahkan!');
            // })

            // // 4. (PENTING) Redirect agar halaman refresh & SweetAlert muncul
            // ->successRedirectUrl(JabatanResource::getUrl('index')),
        ];
    }
}
