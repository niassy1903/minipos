@extends('layouts.app')

@section('title', 'Connexion')
@section('guest-content')
<div class="auth-page d-flex align-items-center justify-content-center p-3">
    <div class="card auth-card w-100 p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="brand-mark mx-auto mb-3"><i class="bi bi-grid-1x2-fill"></i></div>
            <h1 class="h3 mb-2">Bienvenue sur Mini POS</h1>
            <p class="text-muted mb-0">Pilotez vos boutiques depuis un seul espace.</p>
        </div>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3"><label class="form-label fw-semibold">Adresse e-mail</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus></div>
            <div class="mb-3"><label class="form-label fw-semibold">Mot de passe</label><input type="password" name="password" class="form-control" required></div>
            <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label class="form-check-label text-muted" for="remember">Se souvenir de moi</label></div>
            <button class="btn btn-primary w-100 py-2">Se connecter</button>
        </form>
        <p class="text-center text-muted mt-4 mb-0">Pas encore de compte ? <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Créer un compte</a></p>
    </div>
</div>
@endsection