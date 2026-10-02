<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $shops = auth()->user()->shops()->orderBy('name')->get();

        if ($shops->count() === 1) {
            return redirect()->route('shops.dashboard', $shops->first());
        }

        return view('dashboard', compact('shops'));
    }

    public function shop(Shop $shop): View
    {
        $salesQuery = $shop->sales();
        $stats = [
            'revenue' => (clone $salesQuery)->sum('total'),
            'sales' => (clone $salesQuery)->count(),
            'products' => $shop->products()->count(),
            'customers' => $shop->customers()->count(),
        ];
        $lowStock = $shop->products()->where('active', true)->where('stock_quantity', '<=', 5)->orderBy('stock_quantity')->take(5)->get();
        $recentSales = $shop->sales()->with('customer')->latest('sold_at')->take(6)->get();

        return view('shops.dashboard', compact('shop', 'stats', 'lowStock', 'recentSales'));
    }
}
