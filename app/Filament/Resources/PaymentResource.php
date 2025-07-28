<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pesanan')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'id')
                            ->label('Order ID')
                            ->disabled(),
                        Forms\Components\Placeholder::make('customer')
                            ->label('Pelanggan')
                            ->content(fn (Payment $record): ?string => $record->order->user->name ?? '-'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->disabled(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Tanggal Pembayaran')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Bukti Pembayaran')
                    ->schema([
                        // PERBAIKAN: Menggunakan konfigurasi yang lebih andal untuk menampilkan gambar
                        Forms\Components\FileUpload::make('payment_proof')
                            ->label('Gambar Bukti Pembayaran')
                            ->disk('public')
                            ->image() // Menandakan bahwa ini adalah file gambar untuk preview
                            ->downloadable() // Menambahkan tombol untuk mengunduh gambar
                            ->deletable(false) // Menonaktifkan tombol hapus
                            ->disabled() // Membuat field ini read-only
                    ])
                    ->visible(fn ($record) => $record?->payment_proof) // Hanya tampilkan section ini jika ada bukti bayar
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => "#" . str_pad($state, 6, '0', STR_PAD_LEFT)),
                Tables\Columns\TextColumn::make('order.user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('approve')
                    ->label('Approve Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Payment $record): bool => $record->payment_status === 'processing')
                    ->action(function (Payment $record) {
                        DB::transaction(function () use ($record) {
                            $record->update(['payment_status' => 'completed']);
                            $record->order->update(['status' => 'shipped']);
                        });
                        Notification::make()
                            ->title('Pembayaran Disetujui')
                            ->body('Status pesanan telah diubah menjadi "Dikirim".')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembayaran?')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pembayaran ini? Status pesanan akan diubah menjadi "Dikirim". Aksi ini tidak dapat dibatalkan.'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
