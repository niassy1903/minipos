<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Ces identifiants ne correspondent à aucun compte.'])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (Customer::where('email', $value)->exists()) {
                        $fail('Cette adresse e-mail est déjà utilisée par un client.');
                    }
                },
            ],
            'phone' => [
                'required',
                'string',
                'max:40',
                Rule::unique('users', 'phone'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (Customer::where('phone', $value)->exists()) {
                        $fail('Ce numéro de téléphone est déjà utilisé par un client.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('shops.create')->with('success', 'Votre compte est prêt. Créez votre première boutique.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
