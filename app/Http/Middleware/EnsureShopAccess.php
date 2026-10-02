<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $shop = $request->route('shop');

        if (! $shop instanceof Shop || ! $request->user()->shops()->whereKey($shop->id)->exists()) {
            abort(403, 'Vous n’avez pas accès à cette boutique.');
        }

        return $next($request);
    }
}
