@extends('layouts.app')

@section('title', 'Mes boutiques')
@section('content')
<div class="d-flex justify-content-between align-items-start gap-3 mb-4">
    <div><div class="text-uppercase small text-success fw-bold mb-2">Espace administrateur</div><h1 class="page-title h2 mb-1">Mes boutiques</h1><p class="text-muted mb-0">Sélectionnez un point de vente pour commencer.</p></div>
    <a href="{{ route('shops.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nouvelle boutique</a>
</div>
@if($shops->isEmpty())
    <div class="card empty-state p-5 text-center"><i class="bi bi-shop display-5 text-success"></i><h2 class="h4 mt-3">Votre espace est prêt</h2><p class="text-muted">Créez votre première boutique pour ajouter vos produits et enregistrer vos ventes.</p><a href="{{ route('shops.create') }}" class="btn btn-success">Créer une boutique</a></div>
@else
    <div class="row g-4">@foreach($shops as $shop)<div class="col-md-6 col-xl-4"><a href="{{ route('shops.dashboard', $shop) }}" class="card h-100 p-4 text-decoration-none text-reset"><div class="d-flex justify-content-between mb-4"><div class="stat-icon mint"><i class="bi bi-shop"></i></div><i class="bi bi-arrow-up-right text-muted"></i></div><h2 class="h5 mb-1">{{ $shop->name }}</h2><p class="text-muted small mb-4">{{ $shop->address ?: 'Aucune adresse renseignée' }}</p><div class="d-flex gap-3 small text-muted"><span><strong class="text-dark">{{ $shop->products_count }}</strong> produits</span><span><strong class="text-dark">{{ $shop->customers_count }}</strong> clients</span><span><strong class="text-dark">{{ $shop->sales_count }}</strong> ventes</span></div></a></div>@endforeach</div>
@endif
@endsection