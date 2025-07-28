<!DOCTYPE html>
{{-- Menggunakan tema 'cupcake' dari DaisyUI untuk tampilan yang bersih dan modern --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'YourBrand')</title>

    {{-- DaisyUI (sudah termasuk Tailwind CSS) --}}
    {{-- Cukup satu link ini untuk Tailwind dan DaisyUI --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts (Poppins untuk body, Playfair Display untuk aksen) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Custom Styles untuk Font Family --}}
    <style>
        /* Menerapkan font ke seluruh body dan heading */
        body {
            font-family: 'Poppins', sans-serif;
        }
        .font-playfair {
            font-family: 'Playfair Display', serif;
        }
    </style>

    {{-- Blade directive untuk styles tambahan dari halaman child --}}
    @stack('styles')
</head>

<body class="bg-base-100">

{{-- Drawer untuk Navigasi Mobile. Ini adalah container utama --}}
<div class="drawer">
    <input id="mobile-drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content flex flex-col">
        {{-- START: Header / Navbar --}}
        {{-- Dibuat sticky agar selalu terlihat saat scroll --}}
        <header class="w-full navbar bg-base-100 sticky top-0 z-50 shadow-sm">
            {{-- Tombol Hamburger Menu (hanya tampil di mobile) --}}
            <div class="flex-none lg:hidden">
                <label for="mobile-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </label>
            </div>

            {{-- Logo / Nama Brand --}}
            <div class="flex-1 px-2 mx-2">
                <a href="{{ route('home') }}" class="text-2xl font-playfair font-bold text-primary">
                    YourBrand
                </a>
            </div>

            {{-- Menu untuk Desktop (hanya tampil di layar besar) --}}
            <div class="flex-none hidden lg:block">
                <ul class="menu menu-horizontal">
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a></li>
                    <li><a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">My Orders</a></li>
                </ul>
            </div>

            {{-- Aksi di sebelah kanan (Cart & User) --}}
            <div class="flex-none">
                {{-- Ikon Keranjang dengan Indikator --}}
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle">
                        <div class="indicator">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            {{-- Ganti angka ini dengan jumlah item di keranjang --}}
                            <span class="badge badge-sm badge-primary indicator-item">8</span>
                        </div>
                    </label>
                    {{-- Dropdown Content untuk Cart (bisa diisi mini-cart nantinya) --}}
                    <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow">
                        <div class="card-body">
                            <span class="font-bold text-lg">8 Items</span>
                            <span class="text-info">Subtotal: $999</span>
                            <div class="card-actions">
                                <a href="{{ route('cart.index') }}" class="btn btn-primary btn-block">View cart</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Menu User (Login/Register atau Profil) --}}
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            {{-- Placeholder avatar. Bisa diganti dengan foto user jika ada --}}
                            <img alt="User Avatar" src="https://placehold.co/40x40/A6ADBA/333333?text={{ substr(auth()->user()->name ?? 'G', 0, 1) }}" />
                        </div>
                    </label>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="menu-title"><span>Welcome, {{ auth()->user()->name }}</span></li>
                            <li><a href="#">Profile</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit">Logout</button>
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </header>
        {{-- END: Header / Navbar --}}


        {{-- START: Main Content --}}
        {{-- Konten utama dari setiap halaman akan dimuat di sini --}}
        <main class="flex-grow p-4 md:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
        {{-- END: Main Content --}}


        {{-- START: Footer --}}
        {{-- Menggunakan komponen footer dari DaisyUI untuk konsistensi --}}
        <footer class="footer p-10 bg-base-200 text-base-content">
            <aside>
                <p class="text-2xl font-playfair font-bold text-primary">YourBrand</p>
                <p>Elevating everyday luxury since {{ date('Y') }}.<br>Curated collections for the discerning individual.</p>
            </aside>
            <nav>
                <h6 class="footer-title">Quick Links</h6>
                <a href="{{ route('products.index') }}" class="link link-hover">New Arrivals</a>
                <a href="{{ route('cart.index') }}" class="link link-hover">Shopping Cart</a>
                <a href="{{ route('orders.index') }}" class="link link-hover">Order Tracking</a>
            </nav>
            <nav>
                <h6 class="footer-title">Policies</h6>
                <a href="#" class="link link-hover">Privacy Policy</a>
                <a href="#" class="link link-hover">Terms of Service</a>
                <a href="#" class="link link-hover">Return Policy</a>
            </nav>
            <nav>
                <h6 class="footer-title">Connect</h6>
                <a href="mailto:support@yourbrand.com" class="link link-hover">support@yourbrand.com</a>
                <a href="tel:+1234567890" class="link link-hover">+1 (234) 567-890</a>
            </nav>
        </footer>
        {{-- END: Footer --}}

    </div>

    {{-- Konten untuk Drawer Mobile (Sidebar) --}}
    <div class="drawer-side">
        <label for="mobile-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu p-4 w-80 min-h-full bg-base-200">
            {{-- Judul Sidebar --}}
            <li class="menu-title">Menu</li>
            {{-- Link navigasi untuk mobile --}}
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a></li>
            <li><a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">My Orders</a></li>
            <li><a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">Cart</a></li>
            <li><a href="{{ route('checkout.index') }}" class="{{ request()->routeIs('checkout.index') ? 'active' : '' }}">Checkout</a></li>
        </ul>
    </div>
</div>

{{-- Blade directive untuk scripts tambahan dari halaman child --}}
@stack('scripts')
</body>
</html>
