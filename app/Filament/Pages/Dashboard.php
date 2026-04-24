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
        return ! Auth::user()?->isSuperAdmin();
    }
}
