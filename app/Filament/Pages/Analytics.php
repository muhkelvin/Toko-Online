<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Analytics';
    protected static ?string $navigationGroup = 'Analytics';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $title = 'Analytics Dashboard';
    protected static string $view = 'filament.pages.analytics';
    protected static ?string $slug = 'analytics';

    public $totalOrders;
    public $totalRevenue;
    public $totalProducts;
    public $successfulPayments;

    public function mount(): void
    {
        $this->totalOrders = Order::count();
        $this->totalRevenue = Order::sum('total');
        $this->totalProducts = Product::count();
        $this->successfulPayments = Payment::where('payment_status', 'success')->sum('amount');
    }
}
