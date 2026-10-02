<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $shops = $request->user()->shops()->withCount(['products', 'customers', 'sales'])->orderBy('name')->get();

        return view('shops.index', compact('shops'));
    }

    public function create(): View
    {
        return view('shops.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $shop = DB::transaction(function () use ($data, $request) {
            $shop = Shop::create([...$data, 'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5))]);
            $shop->users()->attach($request->user()->id, ['role' => 'owner']);

            return $shop;
        });

        return redirect()->route('shops.dashboard', $shop)->with('success', 'Boutique créée avec succès.');
    }

    public function edit(Shop $shop): View
    {
        $this->ensureMember($shop);

        return view('shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop): RedirectResponse
    {
        $this->ensureMember($shop);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $shop->update($data);

        return redirect()->route('shops.dashboard', $shop)->with('success', 'Boutique mise à jour.');
    }

    public function destroy(Request $request, Shop $shop): RedirectResponse
    {
        $this->ensureMember($shop);
        $shop->delete();

        return redirect()->route('shops.index')->with('success', 'Boutique supprimée.');
    }

    private function ensureMember(Shop $shop): void
    {
        abort_unless($shop->users()->whereKey(Auth::id())->exists(), 403);
    }
}
