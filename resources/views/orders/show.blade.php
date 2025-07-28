@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
    <div class="container mx-auto">
        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div role="alert" class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-3xl lg:text-4xl font-playfair font-bold">Detail Pesanan <span class="text-primary">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span></h1>
            <a href="{{ route('orders.index') }}" class="btn btn-ghost">&larr; Kembali ke Riwayat</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Kolom Kiri: Detail Pengiriman & Pembayaran --}}
            <div class="lg:col-span-1 flex flex-col gap-8">
                {{-- Detail Pengiriman --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Alamat Pengiriman</h2>
                        @if($shippingAddress)
                            <p class="text-base-content/80">
                                {{ $shippingAddress['address'] }}<br>
                                {{ $shippingAddress['city'] }}, {{ $shippingAddress['postal_code'] }}<br>
                                Telp: {{ $shippingAddress['phone'] }}
                            </p>
                        @else
                            <p class="text-base-content/80">Alamat tidak tersedia.</p>
                        @endif
                    </div>
                </div>

                {{-- Detail Status Pembayaran --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Status Pembayaran</h2>
                        @if($order->payment)
                            <p>Metode: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}</span></p>
                            <p>Status:
                                <span class="badge {{ $order->payment->payment_status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($order->payment->payment_status) }}
                            </span>
                            </p>
                            <div class="divider my-2"></div>
                            {{-- Aksi Pembayaran --}}
                            @if($order->payment->payment_method === 'manual_transfer' && $order->payment->payment_status === 'pending')
                                <div class="alert alert-info text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Segera selesaikan pembayaran dan upload bukti transfer Anda.</span>
                                </div>
                                <a href="{{ route('payment.upload.form', $order) }}" class="btn btn-primary w-full mt-4">Upload Bukti</a>
                            @elseif($order->payment->payment_proof)
                                <p class="text-sm">Bukti pembayaran sudah diupload.</p>
                                <a href="{{ asset('storage/' . $order->payment->payment_proof) }}" target="_blank" class="link link-primary text-sm">Lihat Bukti</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Daftar Item & Opsi Pembayaran --}}
            <div class="lg:col-span-2 flex flex-col gap-8">
                {{-- Daftar Item --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Item Pesanan</h2>
                        <div class="space-y-4">
                            @foreach($order->orderItems as $item)
                                <div class="flex items-center gap-4 border-b border-base-200 pb-4 last:border-b-0">
                                    <div class="avatar">
                                        <div class="w-16 rounded">
                                            <img src="{{ $item->product && $item->product->image ? asset('storage/'.$item->product->image) : 'https://placehold.co/100x100' }}" alt="{{ $item->product->name ?? 'Produk Dihapus' }}">
                                        </div>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="font-bold">{{ $item->product->name ?? 'Produk tidak tersedia' }}</p>
                                        <p class="text-sm opacity-70">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="font-semibold">
                                        Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- Ringkasan Total --}}
                        <div class="mt-6 pt-4 border-t border-base-300 space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pengiriman</span>
                                <span>Gratis</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- OPSI PEMBAYARAN - Ditambahkan di sini --}}
                @if($order->payment && $order->payment->payment_method === 'manual_transfer' && $order->payment->payment_status === 'pending')
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title mb-4">Opsi Pembayaran</h2>
                            <p class="text-base-content/80 mb-4">Silakan transfer sejumlah total pesanan ke salah satu rekening di bawah ini:</p>

                            <div class="space-y-4">
                                {{-- Opsi BCA --}}
                                <div class="p-4 border border-base-300 rounded-lg">
                                    <h3 class="font-bold text-lg">BCA (Bank Central Asia)</h3>
                                    <div class="divider my-2"></div>
                                    <p>Nomor Rekening: <span class="font-mono font-semibold">123-456-7890</span></p>
                                    <p>Atas Nama: <span class="font-semibold">PT. Ecommerce Jaya</span></p>
                                </div>

                                {{-- Opsi DANA --}}
                                <div class="p-4 border border-base-300 rounded-lg">
                                    <h3 class="font-bold text-lg">DANA</h3>
                                    <div class="divider my-2"></div>
                                    <p>Nomor Telepon: <span class="font-mono font-semibold">0812-3456-7890</span></p>
                                    <p>Atas Nama: <span class="font-semibold">PT. Ecommerce Jaya</span></p>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <span>PENTING: Pastikan untuk mengupload bukti transfer setelah melakukan pembayaran untuk mempercepat proses verifikasi.</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
