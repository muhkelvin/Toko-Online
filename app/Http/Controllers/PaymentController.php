<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Menampilkan form upload bukti pembayaran untuk order tertentu.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showUploadForm(Order $order)
    {
        // Pastikan user hanya bisa mengakses order miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        // Pastikan order memiliki data pembayaran
        if (!$order->payment) {
            return redirect()->route('orders.show', $order)->with('error', 'Detail pembayaran untuk order ini tidak ditemukan.');
        }

        return view('payments.upload', compact('order'));
    }

    /**
     * Proses upload bukti pembayaran.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadProof(Request $request, Order $order)
    {
        // Pastikan user hanya bisa mengupload untuk order miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak.');
        }

        // Validasi file input
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // maks 5MB
        ]);

        $payment = $order->payment;

        // Hapus bukti pembayaran lama jika ada
        if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        // Simpan file ke storage (disk 'public')
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Update record payment: simpan path gambar dan ubah status menjadi 'processing'
        $payment->update([
            'payment_proof'  => $path,
            'payment_status' => 'processing'
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Bukti pembayaran berhasil dikirim dan sedang menunggu konfirmasi admin.');
    }
}
