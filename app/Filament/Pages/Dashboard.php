<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    /**
     * Sembunyikan default Dashboard di sidebar untuk Super Admin
     * karena Super Admin punya dashboard khusus.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        // Sembunyikan default Dashboard untuk semua role khusus
        return ! ($user?->isSuperAdmin() || $user?->isAdminKomlek() || $user?->isTeknisiKomlek() || $user?->isKaryawan());
    }

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            $url = match (true) {
                $user->isSuperAdmin() => SuperAdminDashboard::getUrl(),
                $user->isAdminKomlek() => AdminKomlekDashboard::getUrl(),
                $user->isTeknisiKomlek() => TeknisiKomlekDashboard::getUrl(),
                $user->isKaryawan() => KaryawanDashboard::getUrl(),
                default => null,
            };

            if ($url) {
                return redirect()->to($url);
            }
        }
    }
}
