<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use App\Models\License; // Import License Model

class ActivateStore extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static string $view = 'filament.pages.activate-store';
    protected static ?string $slug = 'activate';
    protected static bool $shouldRegisterNavigation = false;

    // 👇 1. Create a container for form data
    public ?array $data = [];

    // 👇 2. Mount the form data
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('license_key')
                    ->label('Enter Your License Key')
                    ->required()
                    ->password()
                    ->autofocus(),
            ])
            ->statePath('data'); // 👇 3. Bind form to the $data array
    }

    public function activate()
    {

         //dd('I am running!'); 


        // 👇 4. Get data safely
        $formData = $this->form->getState();
        $inputKey = trim($formData['license_key'] ?? '');

        // Debug: If you still have issues, uncomment the next line to see what's happening
        // dd($inputKey); 

        // --- LICENSE LOGIC ---
        $license = License::where('key', $inputKey)->first();

        if (!$license) {
            Notification::make()->title('Invalid License Key')->danger()->send();
            return;
        }

        if ($license->status !== 'available') {
            Notification::make()->title('This key is already used or suspended.')->danger()->send();
            return;
        }

        if ($license->expires_at && now()->gt($license->expires_at)) {
            Notification::make()->title('This key has expired.')->danger()->send();
            return;
        }

        // --- UPDATE DATABASE ---
        $store = Filament::getTenant();

        // Unlock Store
        $store->forceFill([
            'is_active' => true,
            'activation_key' => $inputKey,
        ])->save();

        // Mark License Used
        $license->forceFill([
            'status' => 'active',
            'store_id' => $store->id,
            'activated_at' => now(),
        ])->save();

        Notification::make()->title('Store Activated!')->success()->send();

        // Redirect to Dashboard home
        return redirect()->to(Filament::getUrl(tenant: $store));
    }
}