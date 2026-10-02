@extends('layouts.app')
@section('title', 'Nouvelle vente')
@section('content')
<div class="d-flex justify-content-between align-items-start gap-3 mb-4"><div><a href="{{ route('sales.index', $shop) }}" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Ventes</a><h1 class="page-title h2 mt-3 mb-1">Nouvelle vente</h1><p class="text-muted mb-0">Le stock sera vérifié et décrémenté automatiquement.</p></div></div>
@if($products->isEmpty())<div class="card empty-state p-5 text-center"><i class="bi bi-box-seam display-5 text-warning"></i><h2 class="h4 mt-3">Aucun produit disponible</h2><p class="text-muted">Ajoutez un produit actif avec du stock avant d’enregistrer une vente.</p><a href="{{ route('products.create', $shop) }}" class="btn btn-primary">Ajouter un produit</a></div>@else
<form method="POST" action="{{ route('sales.store', $shop) }}" id="sale-form">@csrf
<div class="row g-4"><div class="col-xl-8"><div class="card p-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="h5 mb-1">Articles</h2><p class="small text-muted mb-0">Ajoutez les produits et quantités.</p></div><button type="button" class="btn btn-sm btn-outline-success" id="add-line"><i class="bi bi-plus-lg me-1"></i> Ajouter une ligne</button></div><div id="sale-lines"></div><div id="empty-lines" class="text-center text-muted py-4">Ajoutez votre premier produit.</div><div class="border-top pt-3 mt-3 d-flex justify-content-between"><span class="fw-semibold">Total</span><strong class="h4 mb-0" id="sale-total">0 FCFA</strong></div></div></div><div class="col-xl-4"><div class="card p-4"><h2 class="h5 mb-3">Informations</h2><label class="form-label fw-semibold">Client <span class="text-muted fw-normal">(facultatif)</span></label><select class="form-select mb-4" name="customer_id"><option value="">Client comptant</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}{{ $customer->phone ? ' · '.$customer->phone : '' }}</option>@endforeach</select><div class="alert alert-info border-0 small"><i class="bi bi-shield-check me-2"></i>Les quantités sont contrôlées au moment de l’enregistrement pour éviter les ventes en rupture.</div><button class="btn btn-success w-100 py-2 mt-2"><i class="bi bi-check2-circle me-1"></i> Enregistrer la vente</button></div></div></div>
</form>
@endif
@endsection
@push('scripts')
<script>
const products = @json($productData);
const lines = document.getElementById('sale-lines');
const emptyLines = document.getElementById('empty-lines');
const total = document.getElementById('sale-total');
let lineIndex = 0;
const money = value => new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
function refreshTotal() {
    let sum = 0;
    lines.querySelectorAll('.sale-line').forEach(line => {
        const select = line.querySelector('select');
        const qty = Number(line.querySelector('input[type=number]').value || 0);
        const product = products.find(item => String(item.id) === select.value);
        if (product) sum += product.price * qty;
    });
    total.textContent = money(sum);
}
function addLine() {
    const index = lineIndex++;
    const options = products.map(product => `<option value="${product.id}">${product.name} · ${product.reference} · ${money(product.price)} (stock ${product.stock})</option>`).join('');
    const wrapper = document.createElement('div');
    wrapper.className = 'sale-line row g-2 align-items-end mb-3';
    wrapper.innerHTML = `<div class="col-md-7"><label class="form-label small text-muted">Produit</label><select required name="items[${index}][product_id]" class="form-select">${options}</select></div><div class="col-8 col-md-3"><label class="form-label small text-muted">Quantité</label><input required min="1" value="1" type="number" name="items[${index}][quantity]" class="form-control"></div><div class="col-4 col-md-2"><button type="button" class="btn btn-light text-danger w-100 remove-line"><i class="bi bi-trash"></i></button></div>`;
    wrapper.querySelector('select').addEventListener('change', refreshTotal);
    wrapper.querySelector('input').addEventListener('input', refreshTotal);
    wrapper.querySelector('.remove-line').addEventListener('click', () => { wrapper.remove(); emptyLines.classList.toggle('d-none', lines.children.length > 0); refreshTotal(); });
    lines.appendChild(wrapper);
    emptyLines.classList.add('d-none');
    refreshTotal();
}
document.getElementById('add-line')?.addEventListener('click', addLine);
addLine();
</script>
@endpush