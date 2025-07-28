@extends('layouts.app')

@section('title', 'Semua Produk')

@section('content')
    <div class="container mx-auto">
        {{-- Header Halaman: Judul dan Form Pencarian --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
            <h1 class="text-4xl font-playfair font-bold text-neutral">Koleksi Kami</h1>
            <form action="{{ route('products.index') }}" method="GET" class="w-full md:w-auto">
                {{-- Menyimpan filter kategori yang aktif saat melakukan pencarian baru --}}
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="join">
                    <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari produk..." class="input input-bordered join-item w-full md:w-80" />
                    <button type="submit" class="btn btn-primary join-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Layout Utama: Sidebar Kategori + Grid Produk --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- START: Sidebar Kategori --}}
            <aside class="lg:col-span-1">
                <div class="card bg-base-200/50">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Kategori</h2>
                        <ul class="menu bg-base-100 rounded-box">
                            {{-- Link untuk menampilkan semua produk (menghapus filter kategori) --}}
                            <li>
                                <a href="{{ route('products.index', ['q' => $searchQuery]) }}" class="{{ !$selectedCategory ? 'active' : '' }}">
                                    Semua Produk
                                </a>
                            </li>
                            {{-- Loop untuk setiap kategori --}}
                            @foreach ($categories as $category)
                                <li>
                                    {{-- Menambahkan parameter 'q' agar filter pencarian tidak hilang saat mengganti kategori --}}
                                    <a href="{{ route('products.index', ['category' => $category->slug, 'q' => $searchQuery]) }}"
                                       class="{{ $selectedCategory == $category->slug ? 'active' : '' }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>
            {{-- END: Sidebar Kategori --}}


            {{-- START: Grid Produk --}}
            <div class="lg:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse ($products as $product)
                        <div class="card card-compact bg-base-100 shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                            <a href="{{ route('products.show', $product) }}">
                                <figure class="h-64 overflow-hidden">
                                    <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://placehold.co/800x800/EAF0F6/7F8C8D?text=Produk' }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                </figure>
                            </a>
                            <div class="card-body">
                                <h2 class="card-title text-lg">{{ $product->name }}</h2>
                                <p class="text-base-content/70">{{ Str::limit($product->description, 50) }}</p>
                                <div class="mt-2">
                                    <span class="text-xl font-semibold text-primary">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                                {{-- Tombol Aksi Produk --}}
                                <div class="card-actions justify-end mt-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-primary btn-sm">Lihat Detail</a>
                                    <form action="{{ route('cart.store', $product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">+ Keranjang</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Tampilan jika tidak ada produk yang ditemukan --}}
                        <div class="md:col-span-2 xl:col-span-3 text-center py-16">
                            <div class="alert alert-warning max-w-md mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <span>Produk tidak ditemukan.</span>
                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-ghost">Hapus Filter</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Links --}}
                <div class="mt-12">
                    {{-- Penting: appends(request()->query()) agar filter tetap aktif saat pindah halaman --}}
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
            {{-- END: Grid Produk --}}

        </div>
    </div>
@endsection
