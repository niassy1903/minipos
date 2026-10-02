<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Shop $shop): View
    {
        $sales = $shop->sales()->with(['customer', 'user'])->latest('sold_at')->paginate(12);

        return view('sales.index', compact('shop', 'sales'));
    }

    public function create(Shop $shop): View
    {
        $products = $shop->products()->where('active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get();
        $customers = $shop->customers()->orderBy('name')->get();
        $productData = $products->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'reference' => $product->reference,
            'price' => (float) $product->price,
            'stock' => $product->stock_quantity,
        ])->values();

        return view('sales.create', compact('shop', 'products', 'customers', 'productData'));
    }

    public function store(Request $request, Shop $shop): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $sale = DB::transaction(function () use ($data, $shop, $request) {
            $customerId = $data['customer_id'] ?? null;
            if (! empty($customerId) && ! $shop->customers()->whereKey($customerId)->exists()) {
                throw ValidationException::withMessages(['customer_id' => 'Ce client n’appartient pas à cette boutique.']);
            }

            $quantities = collect($data['items'])->groupBy('product_id')->map(fn ($items) => $items->sum('quantity'));
            $products = $shop->products()->where('active', true)->whereIn('id', $quantities->keys())->lockForUpdate()->get()->keyBy('id');

            if ($products->count() !== $quantities->count()) {
                throw ValidationException::withMessages(['items' => 'Un produit sélectionné est introuvable dans cette boutique.']);
            }

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                if ($product->stock_quantity < $quantity) {
                    throw ValidationException::withMessages(['items' => "Stock insuffisant pour {$product->name} (disponible : {$product->stock_quantity})."]);
                }
            }

            $total = $quantities->sum(fn ($quantity, $productId) => (float) $products->get($productId)->price * $quantity);
            $sale = $shop->sales()->create([
                'customer_id' => $customerId ?: null,
                'user_id' => $request->user()->id,
                'sale_number' => 'VTE-'.now()->format('ymdHis').'-'.str()->upper(str()->random(4)),
                'total' => $total,
                'sold_at' => now(),
            ]);

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_reference' => $product->reference,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'line_total' => (float) $product->price * $quantity,
                ]);
                $product->decrement('stock_quantity', $quantity);
            }

            return $sale;
        });

        return redirect()->route('sales.show', [$shop, $sale])->with('success', 'Vente enregistrée et stock mis à jour.');
    }

    public function show(Shop $shop, Sale $sale): View
    {
        abort_unless($sale->shop_id === $shop->id, 404);
        $sale->load(['items', 'customer', 'user']);

        return view('sales.show', compact('shop', 'sale'));
    }
}
