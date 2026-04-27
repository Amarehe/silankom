<?php

namespace App\Filament\Pages\Laporan;

use App\Services\LaporanService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

abstract class BaseLaporanPage extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    /** @var array<string, mixed> */
    public array $statistik = [];

    public bool $showPreview = false;

    public string $periodeLabel = '';

    /**
     * Each subclass must define which report type it handles.
     */
    abstract protected function jenisLaporan(): string;

    /**
     * Each subclass provides its own audit-specific filter fields.
     *
     * @return array<int, \Filament\Forms\Components\Component>
     */
    abstract protected function auditFilters(): array;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'tipe_periode' => 'bulanan',
            'tahun' => now()->year,
            'bulan' => now()->month,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema(array_merge(
                $this->periodeFilters(),
                $this->auditFilters(),
            ))
            ->columns(4)
            ->statePath('data');
    }

    /**
     * Shared period filter fields used by all report pages.
     *
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function periodeFilters(): array
    {
        return [
            Select::make('tipe_periode')
                ->label('Tipe Periode')
                ->options([
                    'bulanan' => '📅 Bulanan',
                    'tahunan' => '📆 Tahunan',
                    'custom' => '🔍 Custom Range',
                ])
                ->required()
                ->default('bulanan')
                ->reactive()
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('tahun')
                ->label('Tahun')
                ->options(function () {
                    $years = [];
                    $currentYear = now()->year;
                    for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                        $years[$y] = (string) $y;
                    }

                    return $years;
                })
                ->default(now()->year)
                ->required()
                ->reactive()
                ->visible(fn ($get) => in_array($get('tipe_periode'), ['bulanan', 'tahunan']))
                ->afterStateUpdated(fn () => $this->resetPreview()),

            Select::make('bulan')
                ->label('Bulan')
                ->options([
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ])
                ->default(now()->month)
                ->required()
                ->reactive()
                ->visible(fn ($get) => $get('tipe_periode') === 'bulanan')
                ->afterStateUpdated(fn () => $this->resetPreview()),

            DatePicker::make('tanggal_dari')
                ->label('Dari Tanggal')
                ->native(false)
                ->reactive()
                ->visible(fn ($get) => $get('tipe_periode') === 'custom')
                ->afterStateUpdated(fn () => $this->resetPreview()),

            DatePicker::make('tanggal_sampai')
                ->label('Sampai Tanggal')
                ->native(false)
                ->reactive()
                ->visible(fn ($get) => $get('tipe_periode') === 'custom')
                ->afterStateUpdated(fn () => $this->resetPreview()),
        ];
    }

    /**
     * Generate preview data and statistics.
     */
    public function tampilkanPreview(): void
    {
        $filters = $this->form->getState();
        $jenis = $this->jenisLaporan();

        $service = new LaporanService;
        $this->statistik = $service->getStatistik($jenis, $filters);
        $this->periodeLabel = $service->getLabelPeriode($filters);
        $this->showPreview = true;
    }

    /**
     * Reset preview when filters change.
     */
    public function resetPreview(): void
    {
        $this->showPreview = false;
        $this->statistik = [];
        $this->periodeLabel = '';
    }

    /**
     * Get preview data collection for the Blade view (Livewire computed property).
     */
    public function getPreviewDataProperty(): ?\Illuminate\Support\Collection
    {
        if (! $this->showPreview) {
            return null;
        }

        $filters = $this->form->getState();
        $jenis = $this->jenisLaporan();
        $service = new LaporanService;

        return $service->{'getQuery'.ucfirst($jenis)}($filters)->get();
    }

    /**
     * Build the export URL with all current filter parameters.
     */
    public function getExportUrl(string $type): string
    {
        $filters = $this->form->getState();
        $jenis = $this->jenisLaporan();

        $params = array_filter($filters, fn ($v) => $v !== null && $v !== '');

        return route("laporan.{$type}", ['jenis' => $jenis]).'?'.http_build_query($params);
    }

    /**
     * Export to PDF — opens in new tab.
     */
    public function exportPdf(): void
    {
        $this->tampilkanPreview();
        $this->js('window.open("'.$this->getExportUrl('pdf').'", "_blank")');
    }

    /**
     * Export to Excel — triggers download.
     */
    public function exportExcel(): void
    {
        $this->tampilkanPreview();
        $this->redirect($this->getExportUrl('excel'));
    }

    /**
     * Print report — opens HTML in new tab and triggers print dialog.
     */
    public function printReport(): void
    {
        $this->tampilkanPreview();
        $this->js('window.open("'.$this->getExportUrl('print').'", "_blank")');
    }
}
