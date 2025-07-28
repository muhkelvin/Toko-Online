<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Menampilkan daftar item yang ada di keranjang user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        // Mengambil item keranjang beserta relasi produknya
        $cartItems = $user->cartItems()->with('product')->get();

        // Menghitung total harga di controller
        $total = $cartItems->reduce(function ($carry, $item) {
            // Pastikan produk ada untuk menghindari error
            if ($item->product) {
                return $carry + ($item->product->price * $item->quantity);
            }
            return $carry;
        }, 0);

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Menambahkan produk ke keranjang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();
        $quantity = $request->input('quantity', 1);

        // Validasi agar kuantitas tidak melebihi stok
        if ($product->inventory < $quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Jika sudah ada, tambahkan jumlahnya
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->inventory < $newQuantity) {
                return back()->with('error', 'Stok produk tidak mencukupi untuk jumlah yang diminta.');
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Jika belum ada, buat record baru
            CartItem::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
                'quantity'   => $quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CartItem $cartItem
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CartItem $cartItem)
    {
        // Pastikan hanya pemilik yang bisa update
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|numeric|min:1']);

        // Validasi stok
        if ($cartItem->product->inventory < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Kuantitas berhasil diperbarui.');
    }


    /**
     * Menghapus item dari keranjang.
     *
     * @param  \App\Models\CartItem  $cartItem
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
