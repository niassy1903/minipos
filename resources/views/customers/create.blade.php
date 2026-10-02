@extends('layouts.app')
@section('title', 'Nouveau client')
@section('content')
<div class="mb-4"><a href="{{ route('customers.index', $shop) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Clients</a><h1 class="page-title h2 mt-3 mb-1">Ajouter un client</h1></div>
<div class="card p-4 p-lg-5" style="max-width:760px"><form method="POST" action="{{ route('customers.store', $shop) }}">@csrf @include('customers.form')<div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('customers.index', $shop) }}" class="btn btn-light">Annuler</a><button class="btn btn-primary">Ajouter le client</button></div></form></div>
@endsection