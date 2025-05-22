@extends('layouts.app')

@section('title', "Enregistrer Paiement pour Facture #{$invoice->invoice_number}")

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Enregistrer un Paiement</h1>
            <p class="text-sm text-gray-600">
                Facture <strong class="text-gray-800">#{{ $invoice->invoice_number }}</strong> de {{ $invoice->supplier->company_name ?? $invoice->purchaseOrder->supplier->company_name }}. <br>
                Montant Dû Actuel: <strong class="text-red-600">{{ number_format($invoice->amount_due, 2, ',', ' ') }} {{-- devise --}}</strong>
            </p>
        </div>
         <a href="{{ route('purchase.invoices.show', $invoice) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour Facture
        </a>
    </div>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs indiquées.</span>
        </div>
    @endif

    <form action="{{ route('purchase.invoices.storePayment', $invoice) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                 <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Détails du Paiement</h3>
                 <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-6">
                    Saisissez les informations relatives à ce règlement.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount_paid_now" class="block text-sm font-medium text-gray-700 mb-1">Montant Payé Maintenant <span class="text-red-500">*</span></label>
                        <input type="number" name="amount_paid_now" id="amount_paid_now" step="0.01" min="0.01" max="{{ $invoice->amount_due }}" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('amount_paid_now') border-red-500 @enderror" value="{{ old('amount_paid_now', number_format($invoice->amount_due, 2, '.', '')) }}" required>
                        @error('amount_paid_now') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Date du Paiement <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('payment_date') border-red-500 @enderror" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                        @error('payment_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Méthode de Paiement <span class="text-red-500">*</span></label>
                        <select name="payment_method" id="payment_method" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('payment_method') border-red-500 @enderror" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="Virement Bancaire" {{ old('payment_method') == 'Virement Bancaire' ? 'selected' : '' }}>Virement Bancaire</option>
                            <option value="Chèque" {{ old('payment_method') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                            <option value="Espèces" {{ old('payment_method') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                            <option value="Carte de Crédit" {{ old('payment_method') == 'Carte de Crédit' ? 'selected' : '' }}>Carte de Crédit</option>
                            <option value="Autre" {{ old('payment_method') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('payment_method') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-1">Référence Paiement (Optionnel)</label>
                        <input type="text" name="payment_reference" id="payment_reference" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('payment_reference') border-red-500 @enderror" value="{{ old('payment_reference') }}" placeholder="N° chèque, ID transaction...">
                        @error('payment_reference') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                 <div class="mt-6">
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes sur le Paiement (Optionnel)</label>
                    <textarea name="payment_notes" id="payment_notes" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('payment_notes') border-red-500 @enderror">{{ old('payment_notes') }}</textarea>
                    @error('payment_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="mt-6">
                    <label for="payment_document" class="block text-sm font-medium text-gray-700 mb-1">Scan Justificatif de Paiement (Optionnel)</label>
                    <input type="file" name="payment_document" id="payment_document" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-somasteel-orange/10 file:text-somasteel-orange hover:file:bg-somasteel-orange/20 @error('payment_document') border-red-500 rounded-md p-1 @enderror">
                    @error('payment_document') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @if($invoice->payment_document_path)
                    <p class="mt-1 text-xs text-gray-500">Actuel: <a href="{{ Storage::url($invoice->payment_document_path) }}" target="_blank" class="text-somasteel-orange hover:underline">Voir</a>. Un nouveau fichier remplacera l'ancien.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end space-x-3">
                 <a href="{{ route('purchase.invoices.show', $invoice) }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-check-double mr-2"></i> Enregistrer le Paiement
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
