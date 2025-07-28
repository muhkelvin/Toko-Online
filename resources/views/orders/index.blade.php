@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-4xl font-playfair font-bold mb-8">Riwayat Pesanan Anda</h1>

        @if($orders->isEmpty())
            <div class="text-center p-12 bg-base-200 rounded-lg">
                <h2 class="text-2xl font-bold">Anda belum memiliki pesanan.</h2>
                <p class="mt-2 mb-6">Semua pesanan yang Anda buat akan muncul di sini.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Jelajahi Koleksi</a>
            </div>
        @else
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Tanggal</th>
                                <th>Status Pesanan</th>
                                <th>Status Pembayaran</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr class="hover">
                                    <td class="font-semibold">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $order->created_at->format('d F Y') }}</td>
                                    <td>
                                        {{-- Logika Badge Baru untuk Status Pesanan --}}
                                        @php
                                            $statusClass = '';
                                            if ($order->status === 'completed') $statusClass = 'badge-success';
                                            elseif ($order->status === 'shipped') $statusClass = 'badge-info';
                                            else $statusClass = 'badge-warning';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->payment)
                                            <span class="badge {{ $order->payment->payment_status === 'completed' ? 'badge-success' : ($order->payment->payment_status === 'processing' ? 'badge-info' : 'badge-warning') }}">
                                                {{ ucfirst($order->payment->payment_status) }}
                                            </span>
                                        @else
                                            <span class="badge badge-error">Error</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-semibold">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td class="text-right space-x-2">
                                        {{-- Tombol Aksi Cepat Kondisional --}}
                                        @if($order->status === 'shipped')
                                            <form action="{{ route('orders.complete', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Pesanan Diterima</button>
                                            </form>
                                        @elseif($order->payment && $order->payment->payment_status === 'pending')
                                            <a href="{{ route('payment.upload.form', $order) }}" class="btn btn-warning btn-sm">Upload Bukti</a>
                                        @endif

                                        {{-- Tombol Detail selalu ada --}}
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline btn-sm">Lihat Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
