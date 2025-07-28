<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman riwayat pesanan milik user yang sedang login.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil pesanan milik user, diurutkan dari yang terbaru.
        // Eager load relasi 'payment' untuk menampilkan status pembayaran di halaman index.
        $orders = Auth::user()->orders()
            ->with('payment')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        // Pastikan user hanya bisa melihat order miliknya sendiri.
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load semua relasi yang dibutuhkan di halaman detail.
        $order->load(['orderItems.product', 'payment']);

        // Mengambil data alamat dari format JSON
        $shippingAddress = json_decode($order->shipping_address, true);

        return view('orders.show', compact('order', 'shippingAddress'));
    }

    public function markAsCompleted(Order $order)
    {
        // Pastikan hanya pemilik order yang bisa melakukan aksi ini
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        // Hanya izinkan perubahan status jika pesanan sedang dalam status 'shipped' (dikirim).
        // Ini mencegah user menyelesaikan pesanan yang belum dikirim.
        if ($order->status === 'shipped') {
            $order->status = 'completed';
            $order->save();

            return redirect()->route('orders.show', $order)->with('success', 'Terima kasih telah mengkonfirmasi pesanan!');
        }

        // Jika status bukan 'shipped', kembalikan dengan pesan error.
        return redirect()->route('orders.show', $order)->with('error', 'Aksi tidak dapat dilakukan pada pesanan ini.');
    }
}
