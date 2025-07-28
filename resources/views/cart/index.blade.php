@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-4xl font-playfair font-bold mb-8">Keranjang Belanja Anda</h1>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div role="alert" class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif


        @if($cartItems->isEmpty())
            <div class="text-center p-12 bg-base-200 rounded-lg">
                <h2 class="text-2xl font-bold">Keranjang Anda masih kosong.</h2>
                <p class="mt-2 mb-6">Sepertinya Anda belum menambahkan produk apapun.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Mulai Belanja</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Daftar Item Keranjang --}}
                <div class="lg:col-span-2">
                    <div class="overflow-x-auto">
                        <table class="table">
                            {{-- Head --}}
                            <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th class="text-center">Kuantitas</th>
                                <th class="text-right">Subtotal</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cartItems as $item)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar">
                                                <div class="mask mask-squircle w-16 h-16">
                                                    <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://placehold.co/100x100' }}" alt="{{ $item->product->name }}" />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">{{ $item->product->name }}</div>
                                                <div class="text-sm opacity-50">{{ $item->product->category->name ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp{{ number_format($item->product->price, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        {{-- Form untuk update kuantitas --}}
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="inline-flex items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" class="input input-bordered input-sm w-20 text-center" min="1" max="{{ $item->product->inventory }}">
                                            <button type="submit" class="btn btn-ghost btn-xs">Update</button>
                                        </form>
                                    </td>
                                    <td class="text-right font-semibold">Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                                    <th>
                                        {{-- Form untuk hapus item --}}
                                        <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs">Hapus</button>
                                        </form>
                                    </th>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Ringkasan Belanja --}}
                <div class="lg:col-span-1">
                    <div class="card bg-base-200 shadow">
                        <div class="card-body">
                            <h2 class="card-title">Ringkasan Belanja</h2>
                            <div class="space-y-2 mt-4">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Pengiriman</span>
                                    <span>Akan dihitung</span>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="card-actions mt-6">
                                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-full">Lanjut ke Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
