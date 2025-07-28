<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama website.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil 4 produk terbaru yang statusnya 'visible'
        $newArrivals = Product::where('is_visible', true)
            ->latest()
            ->take(4)
            ->get();

        // Mengambil 3 kategori secara acak untuk ditampilkan
        $featuredCategories = Category::inRandomOrder()->take(3)->get();

        // Mengambil 4 produk terlaris (disimulasikan dengan random order untuk contoh ini)
        // Implementasi nyata akan membutuhkan query yang lebih kompleks pada tabel order_items
        $topSelling = Product::where('is_visible', true)
            ->inRandomOrder() // Ganti dengan logika terlaris Anda
            ->take(4)
            ->get();

        return view('home', [
            'newArrivals' => $newArrivals,
            'featuredCategories' => $featuredCategories,
            'topSelling' => $topSelling,
        ]);
    }
}
