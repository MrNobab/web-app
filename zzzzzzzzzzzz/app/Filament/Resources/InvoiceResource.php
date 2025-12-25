<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


// Add these to the top of your file
use Filament\Forms\Components\Actions\Action;
use App\Models\Customer;







class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- HEADER SECTION ---
                Section::make()
                    ->columns(2)
                    ->schema([
                        // Select::make('customer_id')
                        //     ->relationship('customer', 'name')
                        //     ->searchable()
                        //     ->required()
                        //     ->createOptionForm([
                        //         TextInput::make('name')->required(),
                        //         TextInput::make('email')->email(),
                        //         TextInput::make('phone'),
                        //     ]),

                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            // 👇 This creates the text button above the input
                            ->hintAction(
                                Action::make('create_customer')
                                    ->label('Add New Customer')
                                    ->icon('heroicon-m-plus')
                                    ->form([
                                        // The form to create the customer
                                        TextInput::make('name')->required(),
                                        TextInput::make('email')->email(),
                                        TextInput::make('phone')->tel(),
                                    ])
                                    ->action(function (array $data, Set $set) {
                                        // 1. Create the customer
                                        $newCustomer = Customer::create($data);
                                        
                                        // 2. Automatically select them in the dropdown
                                        $set('customer_id', $newCustomer->id);
                                    })
                            ),
                        
                        DatePicker::make('date')
                            ->default(now())
                            ->required(),
                    ]),

                // --- ITEMS SECTION ---
                Section::make()
                    ->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->label('Product')
                                ->options(Product::query()->pluck('name', 'id'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_price', $product->price);
                                        $set('unit_price_display', $product->price);
                                        
                                        // Calculate Row Total
                                        $quantity = $get('quantity') ?: 1;
                                        $set('row_total', $product->price * $quantity);
                                    }
                                    // Force total update (using relative path to items)
                                    self::updateTotals($get, $set); 
                                }),

                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(500) // Small delay to let user finish typing
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    $price = $get('unit_price');
                                    $set('row_total', $state * $price);
                                    self::updateTotals($get, $set);
                                }),

                            TextInput::make('unit_price')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->live(500)
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    $qty = $get('quantity');
                                    $set('row_total', $state * $qty);
                                    self::updateTotals($get, $set);
                                }),

                            TextInput::make('row_total')
                                ->numeric()
                                ->prefix('$')
                                ->readOnly(),
                        ])
                        ->columns(4)
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateTotals($get, $set);
                        }),
                    ]),

                // --- FOOTER SECTION ---
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('subtotal')->numeric()->readOnly()->prefix('$'),
                        TextInput::make('tax')->label('Tax (5%)')->numeric()->readOnly()->prefix('$'),
                        TextInput::make('total')->label('Grand Total')->numeric()->readOnly()->prefix('$')->extraInputAttributes(['class' => 'text-xl font-bold']),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#ID')->sortable(),
                TextColumn::make('customer.name')->searchable()->sortable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('items_count')->counts('items')->label('Items'),
                TextColumn::make('total')->money('USD')->sortable()->weight('bold'),
            ])
            
            
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // 👇 ADD THIS ACTION
                Tables\Actions\Action::make('pdf') 
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Invoice $record) => route('invoice.pdf', $record))
                    ->openUrlInNewTab(), 
                    
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    
    public static function updateTotals(Get $get, Set $set): void
    {
        // Try to get items from standard path, or relative path (if inside a row)
        $items = $get('items') ?? $get('../../items') ?? [];

        $subtotal = 0;

        foreach ($items as $item) {
            // Sum up the row totals
            $subtotal += floatval($item['row_total'] ?? 0);
        }

        $tax = $subtotal * 0.05;
        $total = $subtotal + $tax;

        // Use relative pathing to ensure we set the fields at the root level
        // If $get('subtotal') works, we are at root. If not, go up.
        $target = $get('subtotal') !== null ? '' : '../../';

        $set($target . 'subtotal', number_format($subtotal, 2, '.', ''));
        $set($target . 'tax', number_format($tax, 2, '.', ''));
        $set($target . 'total', number_format($total, 2, '.', ''));
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}