@extends('layouts.app')
@section('title', 'Modifier la boutique')
@section('content')
<div class="mb-4"><a href="{{ route('shops.dashboard', $shop) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> {{ $shop->name }}</a><h1 class="page-title h2 mt-3 mb-1">Modifier la boutique</h1></div>
<div class="card p-4 p-lg-5" style="max-width:760px"><form method="POST" action="{{ route('shops.update', $shop) }}">@csrf @method('PUT') @include('shops.form')<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('shops.dashboard', $shop) }}" class="btn btn-light">Annuler</a><button class="btn btn-primary">Enregistrer</button></div></form></div>
@endsection