<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Pastikan Anda meng-import model Category
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    /**
     * Menampilkan halaman daftar produk dengan fungsionalitas filter dan pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Ambil semua input dari request
        $searchQuery = $request->input('q');
        $categorySlug = $request->input('category');

        // 2. Ambil semua kategori untuk ditampilkan di sidebar filter
        $categories = Category::orderBy('name', 'asc')->get();

        // 3. Bangun query produk menggunakan Query Builder
        $productsQuery = Product::query();

        // 4. Terapkan filter berdasarkan Kategori jika ada
        // Menggunakan whereHas untuk memfilter berdasarkan relasi
        $productsQuery->when($categorySlug, function ($query, $slug) {
            return $query->whereHas('category', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        });

        // 5. Terapkan filter berdasarkan Pencarian jika ada
        // Membungkus dalam closure untuk menghindari masalah scope dengan filter lain
        $productsQuery->when($searchQuery, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });

        // 6. Ambil hasil query dengan pagination
        // Menggunakan 12 item per halaman agar pas dengan grid 2, 3, atau 4 kolom
        // Mengurutkan dari yang terbaru sebagai default
        $products = $productsQuery->latest()->paginate(12);

        // 7. Kirim semua data yang diperlukan ke view
        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categorySlug, // Untuk menandai kategori aktif
            'searchQuery' => $searchQuery, // Untuk menampilkan kembali di input search
        ]);
    }

    // Method 'show' dan lainnya tetap sama...
    public function show(Product $product)
    {
        // Inisialisasi koleksi kosong untuk produk terkait
        $relatedProducts = new Collection();

        // Hanya cari produk terkait jika produk saat ini memiliki kategori
        if ($product->categories_id) {
            $relatedProducts = Product::where('categories_id', $product->categories_id)
                ->where('id', '!=', $product->id) // Kecualikan produk yang sedang dilihat
                ->inRandomOrder()
                ->take(4) // Batasi hanya 4 produk
                ->get();
        }

        // Kirim data produk dan produk terkait ke view
        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}
