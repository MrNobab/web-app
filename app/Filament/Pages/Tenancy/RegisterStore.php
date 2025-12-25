<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Store;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterStore extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register your Store';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Store Name')
                    ->placeholder('e.g. My Awesome Electric Shop')
                    ->required(),
            ]);
    }

    protected function handleRegistration(array $data): Store
    {
        // Force active to false
        $data['is_active'] = false; 

        $store = Store::create($data);
        $store->users()->attach(auth()->user());

        return $store;
    }
    
}