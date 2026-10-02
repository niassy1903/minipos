@extends('layouts.app')

@section('title', 'Créer un compte')
@section('guest-content')
<div class="auth-page d-flex align-items-center justify-content-center p-3">
    <div class="card auth-card w-100 p-4 p-md-5">
        <div class="mb-4"><a href="{{ route('login') }}" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Retour à la connexion</a><h1 class="h3 mt-3 mb-2">Créer votre compte</h1><p class="text-muted mb-0">Un compte peut gérer plusieurs boutiques.</p></div>
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="mb-3"><label class="form-label fw-semibold">Nom complet</label><input type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus></div>
            <div class="mb-3"><label class="form-label fw-semibold">Adresse e-mail</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-semibold">Téléphone</label><input type="tel" name="phone" value="{{ old('phone') }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-semibold">Mot de passe</label><input type="password" name="password" class="form-control" required minlength="8"></div>
            <div class="mb-4"><label class="form-label fw-semibold">Confirmer le mot de passe</label><input type="password" name="password_confirmation" class="form-control" required minlength="8"></div>
            <button class="btn btn-primary w-100 py-2">Créer mon compte</button>
        </form>
    </div>
</div>
@endsection