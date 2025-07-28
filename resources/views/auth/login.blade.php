@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="hero min-h-[calc(100vh-280px)] bg-base-200">
        {{-- Mengubah dari lg:flex-row-reverse menjadi lg:flex-row untuk menukar posisi --}}
        <div class="hero-content flex-col lg:flex-row">
            <div class="text-center lg:text-left lg:pr-12">
                <h1 class="text-5xl font-bold font-playfair">Welcome Back!</h1>
                <p class="py-6">Sign in to access your orders, manage your account, and enjoy a seamless shopping experience with us.</p>
            </div>
            <div class="card shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
                <form action="{{ route('login') }}" method="POST" class="card-body">
                    @csrf
                    <h2 class="text-2xl font-bold text-center mb-4">Sign In</h2>

                    {{-- Menampilkan Error Validasi --}}
                    @if($errors->any())
                        <div role="alert" class="alert alert-error text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    {{-- Input Email --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" name="email" placeholder="email@example.com" class="input input-bordered" required autofocus value="{{ old('email') }}" />
                    </div>

                    {{-- Input Password --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="password" class="input input-bordered" required />
                        <label class="label">
                            <a href="#" class="label-text-alt link link-hover">Forgot password?</a>
                        </label>
                    </div>

                    {{-- Remember Me --}}
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-4">
                            <input type="checkbox" name="remember" class="checkbox checkbox-primary" />
                            <span class="label-text">Remember me</span>
                        </label>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>

                    {{-- Link ke Halaman Register --}}
                    <p class="mt-4 text-center text-sm">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="link link-primary">Create one</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection
