<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Exception;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman checkout dengan ringkasan cart.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        // Jika keranjang kosong, redirect ke halaman produk
        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index')->with('info', 'Keranjang Anda kosong, silakan berbelanja terlebih dahulu.');
        }

        // Hitung total harga cart
        $total = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->product->price * $item->quantity);
        }, 0);

        return view('checkout.index', compact('cartItems', 'total'));
    }

    /**
     * Proses checkout.
     *
     * Membuat Order, OrderItem, dan Payment dalam satu database transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
        ]);

        DB::beginTransaction();

        try {
            $total = $cartItems->reduce(function ($carry, $item) {
                return $carry + ($item->product->price * $item->quantity);
            }, 0);

            $order = $user->orders()->create([
                'total'   => $total,
                'status'  => 'pending',
                'shipping_address' => json_encode([
                    'address' => $request->address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                ]),
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                if ($product->inventory < $cartItem->quantity) {
                    throw new Exception('Stok untuk produk ' . $product->name . ' tidak mencukupi.');
                }
                $product->inventory -= $cartItem->quantity;
                $product->save();

                // PERBAIKAN: Menggunakan relasi 'orderItems' yang benar
                $order->orderItems()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity'   => $cartItem->quantity,
                    'price'      => $cartItem->product->price,
                ]);
            }

            $order->payment()->create([
                'payment_method'   => 'manual_transfer',
                'payment_status'   => 'pending',
                'amount'           => $total,
            ]);

            $user->cartItems()->delete();
            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order berhasil dibuat. Silakan upload bukti pembayaran.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')->with('error', 'Terjadi kesalahan saat memproses order: ' . $e->getMessage());
        }
    }}
