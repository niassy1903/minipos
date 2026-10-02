@extends('layouts.app')
@section('title', 'Modifier le produit')
@section('content')
<div class="mb-4"><a href="{{ route('products.index', $shop) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Produits</a><h1 class="page-title h2 mt-3 mb-1">Modifier le produit</h1><p class="text-muted">{{ $product->name }} · {{ $product->reference }}</p></div>
<div class="card p-4 p-lg-5" style="max-width:820px"><form method="POST" action="{{ route('products.update', [$shop, $product]) }}">@csrf @method('PUT') @include('products.form')<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('products.index', $shop) }}" class="btn btn-light">Annuler</a><button class="btn btn-primary">Enregistrer</button></div></form></div>
@endsection