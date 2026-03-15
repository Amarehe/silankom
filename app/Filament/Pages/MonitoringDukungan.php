<?php

namespace App\Filament\Pages;

use App\Models\ReqDukunganModel;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class MonitoringDukungan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartBar;

    protected static ?string $navigationLabel = 'Monitoring Dukungan';

    protected static string|UnitEnum|null $navigationGroup = 'Admin Dukungan';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Monitoring Request Dukungan';

    protected static ?string $slug = 'monitoring-dukungan';

    protected string $view = 'filament.pages.monitoring-dukungan';

    public $lastKnownId;

    public function mount(): void
    {
        $this->lastKnownId = ReqDukunganModel::max('id');
    }

    public function checkNewRequest(): void
    {
        $currentMaxId = ReqDukunganModel::max('id');

        if ($currentMaxId > $this->lastKnownId) {
            $this->lastKnownId = $currentMaxId;

            Notification::make()
                ->title('Request Dukungan Baru!')
                ->body('Terdapat permintaan dukungan kegiatan yang baru masuk.')
                ->success()
                ->send();
        }
    }

    public function getRequests(): Collection
    {
        return ReqDukunganModel::query()
            ->with(['pemohon', 'picDukungan'])
            ->latest()
            ->limit(20)
            ->get();
    }
}
