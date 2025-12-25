<x-filament-panels::page>
    <div class="max-w-lg mx-auto p-6 bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 text-center">
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-2">Activation Required</h2>
            <p class="text-gray-500">
                Your store <strong>{{ filament()->getTenant()->name }}</strong> is currently inactive. 
            </p>
        </div>

        <!-- 👇 CHANGED FORM TO DIV -->
        <div wire:keydown.enter="activate">
            {{ $this->form }}
            
            <!-- 👇 ADDED WIRE:CLICK -->
            <button 
                wire:click="activate"
                class="mt-4 w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded-lg transition"
            >
                <span wire:loading.remove>Activate Store</span>
                <span wire:loading>Checking...</span>
            </button>
        </div>
        
        <!-- Error Message Display -->
        @if ($errors->any())
            <div class="mt-4 text-red-500 text-sm">
                {{ $errors->first() }}
            </div>
        @endif
    </div>
</x-filament-panels::page>
