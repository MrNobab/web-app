<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Invoice #'),
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('total')->money('USD')->weight('bold'),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Items'),
            ])
            ->headerActions([
                // You can add a "New Invoice" button directly here if you want
                Tables\Actions\CreateAction::make(), 
            ])
            ->actions([
                Tables\Actions\Action::make('View')
                    ->url(fn ($record) => \App\Filament\Resources\InvoiceResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
