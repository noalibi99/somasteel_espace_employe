@extends('layouts.app')

@section('title', "Enregistrer Facture pour BDC #{$purchaseOrder->po_number}")

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Enregistrer une Facture Fournisseur</h1>
            <p class="text-sm text-gray-600">
                Pour le Bon de Commande <a href="{{ route('purchase.orders.show', $purchaseOrder) }}" class="text-somasteel-orange hover:underline font-medium">#{{ $purchaseOrder->po_number }}</a>
                de <strong class="text-gray-800">{{ $purchaseOrder->supplier->company_name }}</strong>.
            </p>
        </div>
        <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('purchase.invoices.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour
        </a>
    </div>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
     @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs indiquées dans le formulaire.</span>
        </div>
    @endif

    <form action="{{ route('purchase.invoices.store', $purchaseOrder) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Détails de la Facture</h3>
                 <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Saisissez les informations telles qu'elles apparaissent sur la facture du fournisseur.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-1">N° Facture Fournisseur <span class="text-red-500">*</span></label>
                        <input type="text" name="invoice_number" id="invoice_number" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('invoice_number') border-red-500 @enderror" value="{{ old('invoice_number') }}" required>
                        @error('invoice_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-1">Date Facture <span class="text-red-500">*</span></label>
                        <input type="date" name="invoice_date" id="invoice_date" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('invoice_date') border-red-500 @enderror" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                        @error('invoice_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Date d'Échéance</label>
                        <input type="date" name="due_date" id="due_date" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('due_date') border-red-500 @enderror" value="{{ old('due_date') }}">
                        @error('due_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="amount_ht" class="block text-sm font-medium text-gray-700 mb-1">Montant HT <span class="text-red-500">*</span></label>
                        <input type="number" name="amount_ht" id="amount_ht" step="0.01" min="0" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('amount_ht') border-red-500 @enderror" value="{{ old('amount_ht') }}" required>
                        @error('amount_ht') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="vat_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant TVA <span class="text-red-500">*</span></label>
                        <input type="number" name="vat_amount" id="vat_amount" step="0.01" min="0" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('vat_amount') border-red-500 @enderror" value="{{ old('vat_amount') }}" required>
                        @error('vat_amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant TTC <span class="text-red-500">*</span></label>
                        <input type="number" name="total_amount" id="total_amount" step="0.01" min="0" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('total_amount') border-red-500 @enderror" value="{{ old('total_amount') }}" required>
                        @error('total_amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500">Vérifiez que TTC = HT + TVA.</p>
                    </div>
                </div>
                <div class="mt-6">
                    <label for="invoice_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes sur la Facture (Optionnel)</label>
                    <textarea name="invoice_notes" id="invoice_notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('invoice_notes') border-red-500 @enderror">{{ old('invoice_notes') }}</textarea>
                    @error('invoice_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-6">
                    <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Scan de la Facture (Optionnel)</label>
                    <input type="file" name="document" id="document" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-somasteel-orange/10 file:text-somasteel-orange hover:file:bg-somasteel-orange/20 @error('document') border-red-500 rounded-md p-1 @enderror">
                    @error('document') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-500">Max. 5MB. Formats: PDF, JPG, PNG, DOC, DOCX.</p>
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end space-x-3">
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('purchase.invoices.dashboard') }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-save mr-2"></i> Enregistrer la Facture
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountHt = document.getElementById('amount_ht');
    const vatAmount = document.getElementById('vat_amount');
    const totalAmount = document.getElementById('total_amount');

    function calculateTotal() {
        const ht = parseFloat(amountHt.value) || 0;
        const vat = parseFloat(vatAmount.value) || 0;
        totalAmount.value = (ht + vat).toFixed(2);
    }

    amountHt.addEventListener('input', calculateTotal);
    vatAmount.addEventListener('input', calculateTotal);
});
</script>
@endpush
