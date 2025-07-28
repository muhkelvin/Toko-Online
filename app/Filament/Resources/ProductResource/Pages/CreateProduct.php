<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // Method untuk redirect setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Atau jika ingin menggunakan method alternatif
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Bisa tambahkan logic tambahan sebelum create jika diperlukan
    //     return $data;
    // }

    // Method untuk custom success message (opsional)
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Produk berhasil dibuat!';
    }
}
