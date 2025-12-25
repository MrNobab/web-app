@php
    $store = filament()->getTenant();
    
    $fields = [
        'name', 'email', 'phone', 'address', 'city', 
        'zip_code', 'tax_number', 'logo_path', 'banner_path'
    ];
    
    $filled = 0;
    foreach ($fields as $field) {
        if (!empty($store->$field)) {
            $filled++;
        }
    }
    
    $total = count($fields);
    $percentage = round(($filled / $total) * 100);

    // FIX: Use Hex Codes instead of Tailwind classes to guarantee visibility
    $colorHex = match(true) {
        $percentage < 40 => '#ef4444', // Red (Tailwind 500)
        $percentage < 80 => '#f59e0b', // Amber (Tailwind 500)
        default => '#22c55e',          // Green (Tailwind 500)
    };
    
    $message = match(true) {
        $percentage < 100 => 'Complete your profile to unlock full potential!',
        default => 'Great job! Your profile is complete.',
    };
@endphp

<div class="mb-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
    <div class="flex justify-between items-center mb-2">
        <div>
            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100">Profile Completion</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
        </div>
        <!-- FIX: Apply color via style attribute -->
        <span class="text-2xl font-bold" style="color: {{ $colorHex }}">
            {{ $percentage }}%
        </span>
    </div>

    <!-- The Progress Bar Track -->
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
        <!-- The Progress Bar Fill -->
        <!-- FIX: Apply background-color via style attribute -->
        <div 
            class="h-4 rounded-full transition-all duration-1000 ease-out" 
            style="width: {{ $percentage }}%; background-color: {{ $colorHex }};"
        ></div>
    </div>
    
    @if($percentage < 100)
    <div class="mt-2 text-xs text-gray-400">
        Missing: 
        @foreach($fields as $field)
            @if(empty($store->$field))
                <span class="inline-block bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded mr-1 mb-1 capitalize">
                    {{ str_replace('_', ' ', str_replace('_path', '', $field)) }}
                </span>
            @endif
        @endforeach
    </div>
    @endif
</div>