<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use App\Filament\Pages\ActivateStore;

class CheckStoreStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. SKIP LIVEWIRE REQUESTS (The "Nuclear" Fix)
        // If the URL contains "livewire", let it pass immediately.
        // This stops the middleware from messing with button clicks.
        if (str_contains($request->path(), 'livewire')) {
            return $next($request);
        }

        $store = Filament::getTenant();

        if (!$store) {
            return $next($request);
        }

        // 2. Are we on the activation page? (Check URL string, not Route Name)
        $isActivationUrl = str_ends_with($request->url(), '/activate');

        // SCENARIO A: Store is LOCKED
        if (!$store->is_active) {
            // If we are NOT on the activation URL, force redirect there
            if (!$isActivationUrl) {
                $url = ActivateStore::getUrl(tenant: $store);
                return redirect()->to($url);
            }
        }

        // SCENARIO B: Store is ACTIVE
        if ($store->is_active && $isActivationUrl) {
            return redirect()->to(filament()->getUrl());
        }

        return $next($request);
    }
}