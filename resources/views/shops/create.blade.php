@extends('layouts.app')
@section('title', 'Nouvelle boutique')
@section('content')
<div class="mb-4"><a href="{{ route('shops.index') }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Mes boutiques</a><h1 class="page-title h2 mt-3 mb-1">Créer une boutique</h1><p class="text-muted">Ajoutez un point de vente à votre espace.</p></div>
<div class="card p-4 p-lg-5" style="max-width:760px"><form method="POST" action="{{ route('shops.store') }}">@csrf @include('shops.form')<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('shops.index') }}" class="btn btn-light">Annuler</a><button class="btn btn-primary">Créer la boutique</button></div></form></div>
@endsection