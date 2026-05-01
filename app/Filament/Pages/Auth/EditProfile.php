<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;

class EditProfile extends BaseEditProfile
{
    protected Width|string|null $maxContentWidth = '3xl';

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel(false)
            ->model($this->getUser())
            ->operation('edit')
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->description('NIP tidak dapat diubah. Hubungi administrator jika terdapat kesalahan.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextInput::make('nip')
                            ->label('NIP')
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-identification'),
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(fn (): bool => auth()->user()->isAdmin())
                            ->disabled(fn (): bool => ! auth()->user()->isAdmin())
                            ->dehydrated(fn (): bool => auth()->user()->isAdmin())
                            ->maxLength(255)
                            ->autofocus()
                            ->prefixIcon('heroicon-o-user'),
                    ]),

                Section::make('Ubah Kata Sandi')
                    ->description('Kosongkan jika tidak ingin mengubah kata sandi.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        $this->getPasswordFormComponent()
                            ->label('Kata Sandi Baru')
                            ->prefixIcon('heroicon-o-key'),
                        $this->getPasswordConfirmationFormComponent()
                            ->label('Konfirmasi Kata Sandi Baru')
                            ->prefixIcon('heroicon-o-key'),
                    ]),

                Section::make()
                    ->schema([
                        Placeholder::make('catatan')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                '<div class="flex items-center gap-3 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 dark:border-amber-600/30 dark:bg-amber-900/20">'
                                .'<span class="text-lg leading-none">ℹ️</span>'
                                .'<span class="text-sm text-amber-800 dark:text-amber-200">Untuk perubahan <strong>nama</strong>, <strong>jabatan</strong>, dan <strong>unit kerja</strong>, silakan hubungi administrator.</span>'
                                .'</div>'
                            )),
                    ])
                    ->compact(),
            ]);
    }
}
