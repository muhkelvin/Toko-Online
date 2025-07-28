@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="hero min-h-[calc(100vh-280px)] bg-base-200">
        {{-- Mengubah dari lg:flex-row-reverse menjadi lg:flex-row untuk menukar posisi --}}
        <div class="hero-content flex-col lg:flex-row">
            <div class="text-center lg:text-left lg:pr-12">
                <h1 class="text-5xl font-bold font-playfair">Join Us Today!</h1>
                <p class="py-6">Create an account to start your journey with our curated collection. Get access to exclusive offers and a personalized shopping experience.</p>
            </div>
            <div class="card shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
                <form action="{{ route('register') }}" method="POST" class="card-body">
                    @csrf
                    <h2 class="text-2xl font-bold text-center mb-4">Create Account</h2>

                    {{-- Menampilkan Error Validasi --}}
                    @if($errors->any())
                        <div role="alert" class="alert alert-error text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Input Nama Lengkap --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Full Name</span>
                        </label>
                        <input type="text" name="name" placeholder="John Doe" class="input input-bordered" required autofocus value="{{ old('name') }}" />
                    </div>

                    {{-- Input Email --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" name="email" placeholder="email@example.com" class="input input-bordered" required value="{{ old('email') }}" />
                    </div>

                    {{-- Input Password --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="password" class="input input-bordered" required />
                    </div>

                    {{-- Input Konfirmasi Password --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Confirm Password</span>
                        </label>
                        <input type="password" name="password_confirmation" placeholder="confirm password" class="input input-bordered" required />
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>

                    {{-- Link ke Halaman Login --}}
                    <p class="mt-4 text-center text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}" class="link link-primary">Sign in here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection
