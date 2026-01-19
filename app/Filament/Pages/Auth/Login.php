<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Auth\Pages\Login as PagesLogin;
use Illuminate\Validation\ValidationException;
use Filament\Auth\Http\Responses\LoginResponse;

class Login extends PagesLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('nip')
            ->label('NIP')
            ->placeholder('Masukkan NIP Anda')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'nip' => $data['nip'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.nip' => __('NIP atau Password yang Anda masukkan salah!'),
            'data.password' => __('NIP atau Password yang Anda masukkan salah!'),
        ]);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('Login Aplikasi'))
            // ->color('warning')
            // ->extraAttributes([
            //     // Gabungkan color (untuk teks) dan stroke (untuk icon garis)
            //     'style' => 'color: white !important;'
            // ])
            ->submit('authenticate');
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            // 1. Jalankan proses login bawaan Filament
            $response = parent::authenticate();
            
            // 2. Jika berhasil (tidak error), ambil user yang sedang login
            /** @var User $user */
            $user = Filament::auth()->user();

            // 3. Update kolom last_login
            if ($user) {
                $user->update([
                    'last_login' => now(),
                ]);
            }
    
            return $response;

        } catch (\Exception $e) {
            // Jika login gagal, lempar errornya kembali agar muncul pesan merah
            throw $e;
        }
    }
}
