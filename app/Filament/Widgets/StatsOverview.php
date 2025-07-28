<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Livewire\Attributes\On; // <-- Tambahkan import ini

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // Pastikan ini muncul di atas grafik

    public ?string $filter = 'week'; // Filter default

    /**
     * Method ini akan "mendengarkan" event 'updateStatsOverview'
     * yang dikirim oleh widget lain (dalam kasus ini, SalesChart).
     */
    #[On('updateStatsOverview')]
    public function updateFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    protected function getStats(): array
    {
        $startDate = match ($this->filter) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->subDays(6),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => now(),
        };
        $endDate = Carbon::now();

        // 1. Menghitung Total Pendapatan berdasarkan filter
        $totalRevenue = Order::whereIn('status', ['completed', 'shipped'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // 2. Menghitung Jumlah Pesanan Baru berdasarkan filter
        $newOrdersCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        // 3. Menghitung Pelanggan Baru berdasarkan filter
        $newCustomersCount = User::whereBetween('created_at', [$startDate, $endDate])->count();

        // Deskripsi dinamis berdasarkan filter
        $description = match ($this->filter) {
            'today' => 'Hari ini',
            'week' => 'Dalam 7 hari terakhir',
            'month' => 'Bulan ini',
            'year' => 'Tahun ini',
            default => '',
        };

        return [
            Stat::make('Total Pendapatan', Number::currency($totalRevenue, 'IDR'))
                ->description('Pendapatan ' . $description)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pesanan Baru', $newOrdersCount)
                ->description('Pesanan baru ' . $description)
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
            Stat::make('Pelanggan Baru', $newCustomersCount)
                ->description('Pelanggan baru ' . $description)
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
