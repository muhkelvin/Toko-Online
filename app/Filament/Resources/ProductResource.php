<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Manajemen Toko';

    protected static ?int $navigationSort = 1;

    /**
     * Generate unique slug untuk produk
     */
    private static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        // Query untuk cek apakah slug sudah ada
        $query = Product::where('slug', $slug);

        // Jika sedang edit, ignore record yang sedang diedit
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Loop sampai dapat slug yang unique
        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $query = Product::where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            $counter++;
        }

        return $slug;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Produk')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Produk')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set, Forms\Get $get) {
                                        if ($operation === 'create') {
                                            // Generate unique slug
                                            $uniqueSlug = self::generateUniqueSlug($state);
                                            $set('slug', $uniqueSlug);
                                        } elseif ($operation === 'edit') {
                                            // Untuk edit, ambil ID record yang sedang diedit
                                            $record = $get('../../record');
                                            $recordId = $record ? $record->id : null;
                                            $uniqueSlug = self::generateUniqueSlug($state, $recordId);
                                            $set('slug', $uniqueSlug);
                                        }
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Slug akan dibuat otomatis dari nama produk')
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                RichEditor::make('description')
                                    ->label('Deskripsi')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('Harga & Stok')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),

                                Forms\Components\TextInput::make('inventory')
                                    ->label('Stok (Inventory)')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                            ])->columns(2),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Tampilkan di Toko')
                                    ->helperText('Aktifkan agar produk muncul di halaman depan.')
                                    ->default(true),
                            ]),

                        Forms\Components\Section::make('Kategori')
                            ->schema([
                                Forms\Components\Select::make('categories_id')
                                    ->label('Kategori Produk')
                                    ->options(Category::all()->pluck('name', 'id')->toArray())
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Kategori')
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function (\Closure $set, $state) {
                                                // Generate unique slug untuk kategori juga
                                                $baseSlug = Str::slug($state);
                                                $slug = $baseSlug;
                                                $counter = 1;

                                                while (Category::where('slug', $slug)->exists()) {
                                                    $slug = $baseSlug . '-' . $counter;
                                                    $counter++;
                                                }

                                                $set('slug', $slug);
                                            }),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->disabled()
                                            ->dehydrated()
                                            ->unique(table: 'categories', column: 'slug'),
                                    ]),
                            ]),

                        FileUpload::make('image')
                            ->label('Gambar')
                            ->image()
                            ->disk('s3') // Final storage location
                            ->directory('products') // S3 folder
                            ->visibility('public')
                            ->maxSize(2048)
                            ->nullable()
                            ->columnSpanFull()
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                $filename = now()->timestamp . '-' . $file->getClientOriginalName();

                                return $file->storeAs(
                                    'products',
                                    $filename,
                                    's3'
                                );
                            })
                    ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(60)
                    ->width(60)
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->extraImgAttributes(['loading' => 'lazy']),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Slug berhasil disalin!'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inventory')
                    ->label('Stok')
                    ->sortable()
                    ->color(fn (string $state): string => $state <= 5 ? 'danger' : 'success')
                    ->icon(fn (string $state): string => $state <= 5 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'),

                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('Tampil'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categories_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name'),

                SelectFilter::make('is_visible')
                    ->label('Status Tampil')
                    ->options([
                        '1' => 'Tampil',
                        '0' => 'Tidak Tampil',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
