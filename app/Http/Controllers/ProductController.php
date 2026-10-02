<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request, Shop $shop): View
    {
        $products = $shop->products()
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%')->orWhere('reference', 'like', '%'.$request->string('search').'%')))
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('products.index', compact('shop', 'products'));
    }

    public function create(Shop $shop): View
    {
        return view('products.create', compact('shop'));
    }

    public function store(Request $request, Shop $shop): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'reference' => ['required', 'string', 'max:80', 'unique:products,reference,NULL,id,shop_id,'.$shop->id],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);
        $data['active'] = $request->boolean('active');
        $shop->products()->create($data);

        return redirect()->route('products.index', $shop)->with('success', 'Produit ajouté.');
    }

    public function edit(Shop $shop, Product $product): View
    {
        abort_unless($product->shop_id === $shop->id, 404);

        return view('products.edit', compact('shop', 'product'));
    }

    public function update(Request $request, Shop $shop, Product $product): RedirectResponse
    {
        abort_unless($product->shop_id === $shop->id, 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'reference' => ['required', 'string', 'max:80', 'unique:products,reference,'.$product->id.',id,shop_id,'.$shop->id],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);
        $data['active'] = $request->boolean('active');
        $product->update($data);

        return redirect()->route('products.index', $shop)->with('success', 'Produit mis à jour.');
    }

    public function destroy(Shop $shop, Product $product): RedirectResponse
    {
        abort_unless($product->shop_id === $shop->id, 404);
        $product->delete();

        return back()->with('success', 'Produit supprimé.');
    }
}
