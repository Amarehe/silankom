<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Pages\SuperAdminDashboard;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as ContractLoginResponse;
use Filament\Auth\Pages\Login as PagesLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

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
            ->label('NIP / NRP')
            ->placeholder('Masukkan NIP / NRP Anda')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->placeholder('Masukkan Password Anda')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
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

    public function authenticate(): ?ContractLoginResponse
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

            // 4. Custom Login Redirects based on Role
            if ($user?->isSuperAdmin() || $user?->isAdminKomlek() || $user?->isKaryawan()) {
                $url = match (true) {
                    $user->isSuperAdmin() => SuperAdminDashboard::getUrl(),
                    $user->isAdminKomlek() => \App\Filament\Pages\AdminKomlekDashboard::getUrl(),
                    $user->isKaryawan() => \App\Filament\Pages\KaryawanDashboard::getUrl(),
                    default => '/',
                };

                return new class($url) implements ContractLoginResponse
                {
                    public function __construct(protected string $url) {}

                    public function toResponse($request): \Illuminate\Http\RedirectResponse|\Livewire\Features\SupportRedirects\Redirector
                    {
                        return redirect()->to($this->url);
                    }
                };
            }

            return $response;

        } catch (\Exception $e) {
            // Jika login gagal, lempar errornya kembali agar muncul pesan merah
            throw $e;
        }
    }
}
