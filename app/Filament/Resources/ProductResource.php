<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                    ]),                    
                
                FileUpload::make('image')
                    ->image()
                    ->directory('products'), // Saves images in storage/app/public/products
                
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$') // Adds the $ symbol like your mockup
                    ->required(),
                
                TextInput::make('stock_quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                
                TextColumn::make('name')
                    ->searchable() // Adds the search bar automatically
                    ->sortable(),
                    
                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                    
                TextColumn::make('stock_quantity')
                    ->label('Stock Status')
                    ->badge() // This makes it look like the colored badges in your HTML
                    ->color(fn (string $state): string => match (true) {
                        $state <= 0 => 'danger',  // Red if 0
                        $state < 10 => 'warning', // Orange if low stock
                        default => 'success',     // Green otherwise
                    })
                    ->formatStateUsing(fn (string $state) => $state . ' Units'),

                TextColumn::make('invoice_items_sum_quantity')
                    ->sum('invoiceItems', 'quantity')
                    ->label('Total Sold')
                    ->sortable(),

                TextColumn::make('invoice_items_sum_row_total')
                    ->sum('invoiceItems', 'row_total')
                    ->money('USD')
                    ->label('Total Revenue')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filter by Category'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoiceItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
