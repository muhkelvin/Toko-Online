<x-filament::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Orders -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-bold text-[var(--navy-blue)]">Total Order</h3>
            <p class="text-2xl font-semibold">{{ $totalOrders }}</p>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-bold text-[var(--navy-blue)]">Total Revenue</h3>
            <p class="text-2xl font-semibold">Rp {{ number_format($totalRevenue, 2) }}</p>
        </div>

        <!-- Total Products -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-bold text-[var(--navy-blue)]">Total Produk</h3>
            <p class="text-2xl font-semibold">{{ $totalProducts }}</p>
        </div>

        <!-- Successful Payments -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-bold text-[var(--navy-blue)]">Pembayaran Sukses</h3>
            <p class="text-2xl font-semibold">{{ $successfulPayments }}</p>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-bold text-[var(--navy-blue)]">Pembayaran Pending</h3>
            <p class="text-2xl font-semibold">{{ $pendingPayments }}</p>
        </div>

        <!-- Failed Payments -->
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-bold text-[var(--navy-blue)]">Pembayaran Gagal</h3>
            <p class="text-2xl font-semibold">{{ $failedPayments }}</p>
        </div>
    </div>
</x-filament::page>
