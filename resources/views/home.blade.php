@extends('layouts.app')

@section('title', 'Selamat Datang di YourBrand')

@section('content')
    {{-- Hero Section --}}
    <div class="hero min-h-[60vh] bg-base-200 rounded-box" style="background-image: url(https://placehold.co/1200x600/EAF0F6/7F8C8D?text=YourBrand+Collection);">
        <div class="hero-overlay bg-opacity-40 rounded-box"></div>
        <div class="hero-content text-center text-neutral-content">
            <div class="max-w-md">
                <h1 class="mb-5 text-5xl font-bold font-playfair text-white">Koleksi Eksklusif Untuk Anda</h1>
                <p class="mb-5 text-white/90">Temukan gaya yang mendefinisikan Anda. Kualitas terbaik untuk momen tak terlupakan.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Jelajahi Sekarang</a>
            </div>
        </div>
    </div>

    {{-- Featured Categories Section --}}
    <div class="my-16">
        <h2 class="text-3xl font-playfair font-bold text-center mb-8">Kategori Pilihan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($featuredCategories as $category)
                <div class="card image-full shadow-xl">
                    <figure><img src="{{ $category->image ? asset('storage/'.$category->image) : 'https://placehold.co/400x300' }}" alt="{{ $category->name }}" /></figure>
                    <div class="card-body justify-center items-center">
                        <h2 class="card-title text-2xl text-white">{{ $category->name }}</h2>
                        <div class="card-actions mt-2">
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="btn btn-primary btn-sm">Lihat Koleksi</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- New Arrivals Section --}}
    <div class="my-16">
        <h2 class="text-3xl font-playfair font-bold text-center mb-8">Produk Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($newArrivals as $product)
                <div class="card card-compact bg-base-100 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                    <a href="{{ route('products.show', $product) }}">
                        <figure class="h-64 overflow-hidden">
                            <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/800x800' }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                        </figure>
                    </a>
                    <div class="card-body">
                        <h2 class="card-title text-lg">{{ $product->name }}</h2>
                        <div class="mt-2">
                            <span class="text-xl font-semibold text-primary">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="card-actions justify-end mt-2">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-primary btn-sm">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-base-content/70">Belum ada produk baru.</p>
            @endforelse
        </div>
    </div>

    {{-- Call to Action (CTA) Section --}}
    <div class="my-16 p-12 bg-primary text-primary-content rounded-box text-center">
        <h2 class="text-3xl font-bold mb-2">Diskon Spesial Akhir Pekan!</h2>
        <p class="mb-6">Nikmati penawaran terbatas untuk semua produk favorit Anda. Jangan sampai ketinggalan!</p>
        <a href="{{ route('products.index') }}" class="btn btn-outline border-white text-white hover:bg-white hover:text-primary">Belanja Sekarang</a>
    </div>

@endsection
