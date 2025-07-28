@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container mx-auto">
        {{-- Breadcrumbs untuk Navigasi yang Mudah --}}
        <div class="text-sm breadcrumbs mb-6">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('products.index') }}">Products</a></li>
                {{-- Menampilkan kategori jika produk memilikinya --}}
                @if($product->category)
                    <li><a href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
                @endif
                <li>{{ $product->name }}</li>
            </ul>
        </div>

        <div class="card lg:card-side bg-base-100 shadow-xl">
            {{-- Kolom Gambar Produk --}}
            <figure class="px-10 pt-10 lg:p-0 lg:w-1/2">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/800x800/EAF0F6/7F8C8D?text=Produk' }}" alt="{{ $product->name }}" class="w-full h-auto max-h-[500px] object-contain rounded-xl" />
            </figure>

            {{-- Kolom Detail Produk --}}
            <div class="card-body lg:w-1/2">
                {{-- Kategori Produk --}}
                @if($product->category)
                    <div class="badge badge-primary">{{ $product->category->name }}</div>
                @endif

                {{-- Nama Produk --}}
                <h1 class="card-title text-3xl lg:text-4xl font-bold font-playfair">{{ $product->name }}</h1>

                {{-- Harga dan Stok --}}
                <div class="flex items-center gap-4 my-2">
                    <span class="text-3xl font-semibold text-primary">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                    @if($product->inventory > 0)
                        <div class="badge badge-success gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Stok: {{ $product->inventory }}
                        </div>
                    @else
                        <div class="badge badge-error">Stok Habis</div>
                    @endif
                </div>

                {{-- Deskripsi Produk --}}
                <div class="prose max-w-none text-base-content/80 my-4">
                    <p>{{ $product->description }}</p>
                </div>

                {{-- Form Tambah ke Keranjang --}}
                <div class="card-actions mt-4">
                    <form action="{{ route('cart.store', $product) }}" method="POST" class="w-full">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4">
                            {{-- Input Kuantitas --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Jumlah</span>
                                </label>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->inventory }}" class="input input-bordered w-full sm:w-24" {{ $product->inventory < 1 ? 'disabled' : '' }} />
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="form-control self-end flex-1">
                                <button type="submit" class="btn btn-primary w-full" {{ $product->inventory < 1 ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    + Tambah ke Keranjang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Bagian Spesifikasi Produk --}}
                <div class="mt-8 pt-4 border-t border-base-200">
                    <h3 class="text-lg font-semibold mb-2">Spesifikasi Produk</h3>
                    <ul class="space-y-1 text-sm text-base-content/80">
                        <li><strong>Kategori:</strong> {{ $product->category->name ?? 'Tidak ada kategori' }}</li>
                        <li><strong>Stok Tersedia:</strong> {{ $product->inventory }} unit</li>
                        <li><strong>Terakhir Diperbarui:</strong> {{ $product->updated_at->format('d F Y') }}</li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- Bagian Produk Terkait (Sekarang Aktif) --}}
        <div class="mt-16">
            <h2 class="text-3xl font-playfair font-bold text-center mb-8">Anda Mungkin Juga Suka</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @isset($relatedProducts)
                    @forelse($relatedProducts as $related)
                        <div class="card card-compact bg-base-100 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                            <a href="{{ route('products.show', $related) }}">
                                <figure class="h-56"><img src="{{ $related->image ? asset('storage/'.$related->image) : 'https://placehold.co/400x400' }}" alt="{{ $related->name }}" class="w-full h-full object-cover" /></figure>
                            </a>
                            <div class="card-body">
                                <h3 class="card-title text-md">{{ $related->name }}</h3>
                                <p class="text-primary font-semibold">Rp{{ number_format($related->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-base-content/60">
                            <p>Tidak ada produk serupa.</p>
                        </div>
                    @endforelse
                @endisset
            </div>
        </div>
    </div>
@endsection
