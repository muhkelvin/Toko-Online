<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $totalOrders }}</h2>
            <p class="text-gray-600">Total Pesanan</p>
        </div>
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Rp {{ number_format($totalRevenue, 2) }}
            </h2>
            <p class="text-gray-600">Total Pendapatan</p>
        </div>
        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</h2>
            <p class="text-gray-600">Total Produk</p>
        </div>
        <!-- Successful Payments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800">
                Rp {{ number_format($successfulPayments, 2) }}
            </h2>
            <p class="text-gray-600">Pembayaran Sukses</p>
        </div>
    </div>
</x-filament::page>
