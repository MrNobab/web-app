<?php

namespace App\Filament\TopAdmin\Resources;

use App\Filament\TopAdmin\Resources\LicenseResource\Pages;
use App\Filament\TopAdmin\Resources\LicenseResource\RelationManagers;
use App\Models\License;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn; // 👈 Import this for quick editing
use Illuminate\Support\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Key Generation
                TextInput::make('key')
                    ->default(fn () => 'PRO-' . strtoupper(Str::random(10)))
                    ->required()
                    ->readOnly(),

                // Status Management
                Select::make('status')
                    ->options([
                        'available' => 'Available (Unused)',
                        'active' => 'Active (In Use)',
                        'suspended' => 'Suspended (Deactivated)',
                    ])
                    ->required()
                    ->default('available'),
                
                // Dates
                DatePicker::make('expires_at')
                    ->label('Expiry Date')
                    ->native(false), // Uses a nicer date picker
                
                TextInput::make('created_at')
                    ->label('Generation Date')
                    ->disabled() // Read only
                    ->placeholder('Will be set on creation'),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                
                // 👇 Quick Activate/Deactivate directly in the table
                SelectColumn::make('status')
                    ->options([
                        'available' => 'Available',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                    ])
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function ($record, $state) {
                        // Logic to immediately lock/unlock the store if you change status
                        if ($record->store) {
                            $isActive = $state === 'active';
                            $record->store->update(['is_active' => $isActive]);
                        }
                    }),

                TextColumn::make('store.name')
                    ->label('Used By')
                    ->placeholder('Unused')
                    ->searchable(),

                // Dates you requested
                TextColumn::make('created_at')
                    ->label('Generated')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date('M d, Y')
                    ->color(fn ($state) => $state && now()->gt($state) ? 'danger' : 'gray') // Red if expired
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
