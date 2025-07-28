@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container mx-auto">
        <h1 class="text-4xl font-playfair font-bold mb-8 text-center">Checkout</h1>

        {{-- Notifikasi Info atau Error --}}
        @if (session('info'))
            <div role="alert" class="alert alert-info mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                {{-- Kolom Kiri: Alamat Pengiriman --}}
                <div class="lg:col-span-3">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h2 class="card-title text-2xl mb-4">Alamat Pengiriman</h2>
                            <div class="space-y-4">
                                {{-- Alamat Lengkap --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Alamat Lengkap</span></label>
                                    <textarea name="address" class="textarea textarea-bordered h-24" placeholder="Jalan, nomor rumah, RT/RW" required>{{ old('address', auth()->user()->address) }}</textarea>
                                    @error('address') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Kota --}}
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">Kota / Kabupaten</span></label>
                                        <input type="text" name="city" placeholder="e.g. Jakarta Selatan" class="input input-bordered" required value="{{ old('city', auth()->user()->city) }}">
                                        @error('city') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    {{-- Kode Pos --}}
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">Kode Pos</span></label>
                                        <input type="text" name="postal_code" placeholder="e.g. 12345" class="input input-bordered" required value="{{ old('postal_code', auth()->user()->postal_code) }}">
                                        @error('postal_code') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                {{-- Nomor Telepon --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Nomor Telepon</span></label>
                                    <input type="tel" name="phone" placeholder="e.g. 08123456789" class="input input-bordered" required value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Ringkasan Pesanan --}}
                <div class="lg:col-span-2">
                    <div class="card bg-base-200 shadow-xl sticky top-24">
                        <div class="card-body">
                            <h2 class="card-title text-2xl mb-4">Ringkasan Pesanan</h2>
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                @foreach($cartItems as $item)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="truncate w-3/4">{{ $item->product->name }} <span class="font-bold">x{{ $item->quantity }}</span></span>
                                        <span class="font-semibold">Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="divider my-4"></div>
                            <div class="space-y-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Subtotal</span>
                                    <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Pengiriman</span>
                                    <span>Gratis</span>
                                </div>
                            </div>
                            <div class="divider my-4"></div>
                            <div class="flex justify-between font-bold text-xl">
                                <span>Total Pembayaran</span>
                                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="card-actions mt-6">
                                <button type="submit" class="btn btn-primary w-full">Buat Pesanan</button>
                            </div>
                            <p class="text-xs text-center mt-2">Dengan membuat pesanan, Anda menyetujui Syarat & Ketentuan kami.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
