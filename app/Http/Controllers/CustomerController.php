<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request, Shop $shop): View
    {
        $customers = $shop->customers()
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%')->orWhere('email', 'like', '%'.$request->string('search').'%')->orWhere('phone', 'like', '%'.$request->string('search').'%')))
            ->withCount('sales')->orderBy('name')->paginate(10)->withQueryString();

        return view('customers.index', compact('shop', 'customers'));
    }

    public function create(Shop $shop): View
    {
        return view('customers.create', compact('shop'));
    }

    public function store(Request $request, Shop $shop): RedirectResponse
    {
        $shop->customers()->create($request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => $this->contactRules('email'),
            'phone' => $this->contactRules('phone'),
            'address' => ['nullable', 'string', 'max:255'],
        ]));

        return redirect()->route('customers.index', $shop)->with('success', 'Client ajouté.');
    }

    public function edit(Shop $shop, Customer $customer): View
    {
        abort_unless($customer->shop_id === $shop->id, 404);

        return view('customers.edit', compact('shop', 'customer'));
    }

    public function update(Request $request, Shop $shop, Customer $customer): RedirectResponse
    {
        abort_unless($customer->shop_id === $shop->id, 404);
        $customer->update($request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => $this->contactRules('email', $customer),
            'phone' => $this->contactRules('phone', $customer),
            'address' => ['nullable', 'string', 'max:255'],
        ]));

        return redirect()->route('customers.index', $shop)->with('success', 'Client mis à jour.');
    }

    public function destroy(Shop $shop, Customer $customer): RedirectResponse
    {
        abort_unless($customer->shop_id === $shop->id, 404);
        $customer->delete();

        return back()->with('success', 'Client supprimé.');
    }

    private function contactRules(string $field, ?Customer $customer = null): array
    {
        $rules = ['nullable', 'string', 'max:'.($field === 'email' ? 255 : 40)];

        if ($field === 'email') {
            $rules[] = 'email';
        }

        $rules[] = Rule::unique('customers', $field)->ignore($customer);
        $rules[] = function (string $attribute, mixed $value, \Closure $fail) use ($field): void {
            if (User::where($field, $value)->exists()) {
                $fail("Ce {$field} est déjà utilisé par un administrateur.");
            }
        };

        return $rules;
    }
}
