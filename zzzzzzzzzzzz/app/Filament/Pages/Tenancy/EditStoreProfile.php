<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

//Extra components if needed in future
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Grid;

class EditStoreProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Store Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. The Progress Bar (Injected as a View)
                Section::make()
                    ->schema([
                        View::make('filament.components.store-completion'),
                    ])
                    ->columnSpanFull(),

                // 2. Branding Section
                Section::make('Branding')
                    ->description('Upload your logo and store banner.')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('logo_path')
                                ->label('Store Logo')
                                ->image()
                                ->avatar() // Makes it round
                                ->directory('store-logos'),
                            
                            FileUpload::make('banner_path')
                                ->label('Store Banner')
                                ->image()
                                ->directory('store-banners')
                                ->columnSpan(1),
                        ]),
                    ]),

                // 3. Basic Details Section
                Section::make('Store Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Store Name')
                            ->required(),
                        
                        TextInput::make('tax_number')
                            ->label('Tax / VAT ID')
                            ->placeholder('e.g. US-123456789'),

                        TextInput::make('website')
                            ->prefix('https://')
                            ->url(),
                    ])->columns(2),

                // 4. Contact Information
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->required(),
                        
                        TextInput::make('phone')
                            ->tel()
                            ->required(),
                        
                        Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),
                        
                        Grid::make(2)->schema([
                            TextInput::make('city'),
                            TextInput::make('zip_code'),
                        ]),
                    ])->columns(2),
            ]);
    }
}