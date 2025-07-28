<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    /**
     * Menambahkan tombol aksi kustom di bagian header halaman.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Tombol Aksi Kustom untuk Menyetujui Pembayaran
            Action::make('approve')
                ->label('Approve Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                // Hanya tampilkan tombol ini jika statusnya 'processing'
                ->visible(fn (Payment $record): bool => $record->payment_status === 'processing')
                ->action(function (Payment $record) {
                    // Gunakan transaction untuk memastikan kedua update berhasil
                    DB::transaction(function () use ($record) {
                        // 1. Update status pembayaran menjadi 'completed'
                        $record->update(['payment_status' => 'completed']);
                        // 2. Update status pesanan menjadi 'shipped' (dikirim)
                        $record->order->update(['status' => 'shipped']);
                    });
                    // Kirim notifikasi sukses
                    Notification::make()
                        ->title('Pembayaran Disetujui')
                        ->body('Status pesanan telah diubah menjadi "Dikirim".')
                        ->success()
                        ->send();

                    // Refresh halaman untuk memperbarui status
                    $this->refreshFormData(['payment_status']);
                })
                ->requiresConfirmation()
                ->modalHeading('Setujui Pembayaran?')
                ->modalDescription('Apakah Anda yakin ingin menyetujui pembayaran ini? Status pesanan akan diubah menjadi "Dikirim". Aksi ini tidak dapat dibatalkan.'),

            // Tombol Edit bawaan Filament (opsional, bisa dihapus jika tidak perlu)
            Actions\EditAction::make(),
        ];
    }
}
