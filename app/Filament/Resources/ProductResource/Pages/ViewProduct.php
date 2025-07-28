<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * Menambahkan tombol aksi di bagian header halaman.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            // Menambahkan tombol "Edit" standar dari Filament
            // yang akan mengarahkan ke halaman EditProduct.
            Actions\EditAction::make(),
        ];
    }
}
