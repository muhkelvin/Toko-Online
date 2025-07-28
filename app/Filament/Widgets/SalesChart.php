<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    // PERBAIKAN: Menyesuaikan tipe data properti agar cocok dengan kelas induk
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'week';

    /**
     * Method ini adalah "lifecycle hook" dari Livewire.
     * Ia akan berjalan setiap kali properti $filter diperbarui.
     */
    public function updated(string $property): void
    {
        if ($property === 'filter') {
            // Mengirim event 'updateStatsOverview' dengan data filter yang baru
            $this->dispatch('updateStatsOverview', filter: $this->filter);
        }
    }

    public function getHeading(): string
    {
        switch ($this->filter) {
            case 'today':
                return 'Grafik Penjualan (Hari Ini)';
            case 'month':
                return 'Grafik Penjualan (Bulan Ini)';
            case 'year':
                return 'Grafik Penjualan (Tahun Ini)';
            case 'week':
            default:
                return 'Grafik Penjualan (7 Hari Terakhir)';
        }
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => '7 Hari Terakhir',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $data = [];
        $labels = [];

        switch ($activeFilter) {
            case 'today':
                $salesData = Order::query()
                    ->whereIn('status', ['completed', 'shipped'])
                    ->whereDate('created_at', today())
                    ->select(
                        DB::raw('HOUR(created_at) as hour'),
                        DB::raw('sum(total) as total_sales')
                    )
                    ->groupBy('hour')->orderBy('hour')->pluck('total_sales', 'hour')->all();

                for ($i = 0; $i < 24; $i++) {
                    $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                    $data[] = $salesData[$i] ?? 0;
                }
                break;

            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $salesData = $this->getSalesDataForPeriod($startDate, $endDate);

                $period = CarbonPeriod::create($startDate, '1 day', $endDate);
                foreach ($period as $date) {
                    $labels[] = $date->format('d M');
                    $data[] = $salesData[$date->format('Y-m-d')] ?? 0;
                }
                break;

            case 'year':
                $salesData = Order::query()
                    ->whereIn('status', ['completed', 'shipped'])
                    ->whereYear('created_at', today()->year)
                    ->select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('sum(total) as total_sales')
                    )
                    ->groupBy('month')->orderBy('month')->pluck('total_sales', 'month')->all();

                for ($i = 1; $i <= 12; $i++) {
                    $labels[] = Carbon::create(null, $i, 1)->format('M');
                    $data[] = $salesData[$i] ?? 0;
                }
                break;

            case 'week':
            default:
                $startDate = now()->subDays(6);
                $endDate = now();
                $salesData = $this->getSalesDataForPeriod($startDate, $endDate);

                $period = CarbonPeriod::create($startDate, '1 day', $endDate);
                foreach ($period as $date) {
                    $labels[] = $date->format('d M');
                    $data[] = $salesData[$date->format('Y-m-d')] ?? 0;
                }
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getSalesDataForPeriod(Carbon $startDate, Carbon $endDate): array
    {
        return Order::query()
            ->whereIn('status', ['completed', 'shipped'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('sum(total) as total_sales')
            )
            ->groupBy('date')->orderBy('date')->pluck('total_sales', 'date')->all();
    }

    protected function getType(): string
    {
        return 'line';
    }
}
