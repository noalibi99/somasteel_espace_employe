@extends('layouts.app')

@section('title', "Créer une Demande de Prix (RFQ)")

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Créer une Demande de Prix (RFQ)</h1>
         <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('purchase.rfq.dashboard') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour
        </a>
    </div>
    <p class="text-sm text-gray-600 mb-4">Pour la demande d'achat : <a href="{{ route('purchase.requests.show', $purchaseRequest) }}" class="text-somasteel-orange hover:underline font-medium">#{{ $purchaseRequest->id }}</a> par {{ $purchaseRequest->user->nom }} {{ $purchaseRequest->user->prénom }}.</p>

    @include('layouts.partials.flash_messages', ['hideGlobalErrors' => true])
    @if ($errors->any() && !$errors->has('suppliers_rfq') && !$errors->has('suppliers_rfq.*') && !$errors->has('email_subject') && !$errors->has('email_body') && !$errors->has('deadline_for_offers') && !$errors->has('notes'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
            <strong class="font-bold">Oups !</strong>
            <span class="block sm:inline">Veuillez corriger les erreurs indiquées.</span>
        </div>
    @endif

    <form action="{{ route('purchase.rfq.store', $purchaseRequest) }}" method="POST" id="createRfqForm" class="space-y-8">
        @csrf
        <input type="hidden" name="send_emails_action" id="send_emails_action" value="0"> {{-- Géré par JS --}}

        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Détails de la Demande d'Achat Initiale</h3>
                 <div class="mt-2 text-sm text-gray-700 space-y-2">
                    <p><strong>Description:</strong> {{ $purchaseRequest->description }}</p>
                    <p class="font-medium">Articles Demandés :</p>
                    <ul class="list-disc list-inside pl-4 space-y-1">
                        @foreach($purchaseRequest->lines as $line)
                            <li>
                                {{ $line->quantity }} x
                                @if($line->article)
                                    <strong>{{ $line->article->designation }}</strong> (Réf: {{ $line->article->reference ?? 'N/A' }})
                                @else
                                    Article non spécifié (ID: {{ $line->article_id }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>


        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations du RFQ</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner les Fournisseurs pour le RFQ <span class="text-red-500">*</span></label>
                        @if($suppliers->isEmpty())
                            <p class="text-sm text-gray-500 bg-yellow-50 p-3 rounded-md">Aucun fournisseur disponible. Veuillez <a href="{{ route('purchase.suppliers.create') }}" class="text-somasteel-orange hover:underline">en ajouter un</a>.</p>
                        @else
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-3">
                            @foreach($suppliers as $supplier)
                            <div class="flex items-start p-2 rounded-md hover:bg-gray-50 transition-colors">
                                <div class="flex items-center h-5">
                                    <input class="supplier-rfq-checkbox h-4 w-4 text-somasteel-orange border-gray-300 rounded focus:ring-somasteel-orange"
                                           type="checkbox"
                                           name="suppliers_rfq[]"
                                           value="{{ $supplier->id }}"
                                           id="supplier_rfq_{{ $supplier->id }}"
                                           {{ (is_array(old('suppliers_rfq')) && in_array($supplier->id, old('suppliers_rfq'))) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm flex-grow">
                                    <label for="supplier_rfq_{{ $supplier->id }}" class="font-medium text-gray-700 cursor-pointer">{{ $supplier->name }}</label>
                                    <p class="text-gray-500">{{ $supplier->contact_email ?? 'Email non fourni' }}</p>
                                </div>
                                <div class="supplier-email-option ml-auto pl-3" style="display: none;">
                                     <div class="flex items-center">
                                        <input class="supplier-email-checkbox h-4 w-4 text-somasteel-orange border-gray-300 rounded focus:ring-somasteel-orange"
                                               type="checkbox"
                                               name="suppliers_to_email[]"
                                               value="{{ $supplier->id }}"
                                               id="supplier_email_{{ $supplier->id }}"
                                               {{ (is_array(old('suppliers_to_email')) && in_array($supplier->id, old('suppliers_to_email'))) ? 'checked' : '' }}
                                               {{ !$supplier->contact_email ? 'disabled' : '' }}>
                                        <label for="supplier_email_{{ $supplier->id }}" class="ml-2 text-xs text-gray-600 cursor-pointer">Notifier</label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @error('suppliers_rfq') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('suppliers_rfq.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="deadline_for_offers" class="block text-sm font-medium text-gray-700 mb-1">Date limite pour réception des offres</label>
                        <input type="datetime-local" name="deadline_for_offers" id="deadline_for_offers"
                               class="w-full md:w-2/3 border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('deadline_for_offers') border-red-500 @enderror"
                               value="{{ old('deadline_for_offers') }}">
                        @error('deadline_for_offers') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes Internes (optionnel)</label>
                        <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-none @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg" id="email_customization_card" style="display:none;">
            <div class="px-4 py-5 sm:p-6">
                 <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">Personnalisation de l'Email</h3>
                 <p class="mt-1 max-w-2xl text-sm text-gray-500 mb-4">
                    Modèle d'email à envoyer aux fournisseurs cochés "Notifier".
                    Placeholders: <code>{supplier_name}</code>, <code>{rfq_id}</code>, <code>{deadline_for_offers}</code>, <code>{company_name}</code>.
                </p>
                <div class="space-y-6">
                    <div>
                        <label for="email_subject" class="block text-sm font-medium text-gray-700 mb-1">Objet de l'email <span class="text-red-500">*</span></label>
                        <input type="text" name="email_subject" id="email_subject" class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange @error('email_subject') border-red-500 @enderror" value="{{ old('email_subject', 'Nouvelle Demande de Prix (RFQ#{rfq_id}) de {company_name}') }}">
                        @error('email_subject') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email_body" class="block text-sm font-medium text-gray-700 mb-1">Corps du message <span class="text-red-500">*</span></label>
                        <textarea name="email_body" id="email_body" rows="8" class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange resize-y @error('email_body') border-red-500 @enderror">{{ old('email_body', "Cher {supplier_name},\n\nNous vous invitons à nous soumettre votre meilleure offre pour la demande de prix RFQ#{rfq_id}.\nLa date limite de réception des offres est fixée au {deadline_for_offers}.\n\nCordialement,\nLe Service Achat") }}</textarea>
                        @error('email_body') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end space-x-3">
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('purchase.rfq.dashboard') }}"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    Annuler
                </a>
                <button type="button" id="createRfqDraftButton" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-save mr-2"></i> Créer RFQ (Brouillon)
                </button>
                <button type="button" id="createRfqAndSendEmailsButton" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-paper-plane mr-2"></i> Créer RFQ et Envoyer Emails
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Le JavaScript que vous aviez pour gérer les checkboxes et la soumission
    // des emails est conservé. Assurez-vous qu'il fonctionne bien avec
    // la nouvelle structure HTML.
    document.addEventListener('DOMContentLoaded', function () {
        const rfqCheckboxes = document.querySelectorAll('.supplier-rfq-checkbox');
        // const emailOptions = document.querySelectorAll('.supplier-email-option'); // Plus besoin, on cible dynamiquement
        const emailCard = document.getElementById('email_customization_card');
        const emailSubjectInput = document.getElementById('email_subject');
        const emailBodyInput = document.getElementById('email_body');
        const createRfqForm = document.getElementById('createRfqForm');
        const sendEmailsActionInput = document.getElementById('send_emails_action');
        const createRfqDraftButton = document.getElementById('createRfqDraftButton');
        const createRfqAndSendEmailsButton = document.getElementById('createRfqAndSendEmailsButton');

        function updateEmailOptionsVisibility() {
            let anyRfqChecked = false;
            rfqCheckboxes.forEach(checkbox => {
                const emailOptionDiv = checkbox.closest('.list-group-item, .flex.items-start').querySelector('.supplier-email-option'); // Adapté pour la nouvelle structure
                if (checkbox.checked) {
                    anyRfqChecked = true;
                    if (emailOptionDiv) emailOptionDiv.style.display = 'flex'; // 'flex' pour aligner correctement
                } else {
                    if (emailOptionDiv) {
                        emailOptionDiv.style.display = 'none';
                        const emailCheckbox = emailOptionDiv.querySelector('.supplier-email-checkbox');
                        if (emailCheckbox) emailCheckbox.checked = false;
                    }
                }
            });
            updateEmailCustomizationVisibility();
        }

        function updateEmailCustomizationVisibility() {
            let anyEmailChecked = false;
            document.querySelectorAll('.supplier-email-checkbox:checked').forEach(cb => {
                 // Vérifier si l'option email parente est visible
                if (cb.closest('.supplier-email-option').style.display !== 'none') {
                    anyEmailChecked = true;
                }
            });

            if (anyEmailChecked) {
                emailCard.style.display = 'block';
                emailSubjectInput.required = true;
                emailBodyInput.required = true;
            } else {
                emailCard.style.display = 'none';
                emailSubjectInput.required = false;
                emailBodyInput.required = false;
            }
        }

        rfqCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateEmailOptionsVisibility);
        });

        // Délégation d'événement pour les checkboxes d'email car elles peuvent être masquées/affichées
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('supplier-email-checkbox')) {
                updateEmailCustomizationVisibility();
            }
        });

        createRfqDraftButton.addEventListener('click', function() {
            sendEmailsActionInput.value = '0';
            // Temporairement désactiver la validation 'required' des champs email
            const initialRequiredSubject = emailSubjectInput.required;
            const initialRequiredBody = emailBodyInput.required;
            emailSubjectInput.required = false;
            emailBodyInput.required = false;

            createRfqForm.submit();

            // Rétablir la validation après soumission (si nécessaire, mais le rechargement de page le fera)
            // emailSubjectInput.required = initialRequiredSubject;
            // emailBodyInput.required = initialRequiredBody;
        });

        createRfqAndSendEmailsButton.addEventListener('click', function() {
            sendEmailsActionInput.value = '1';
             // Assurer que les champs email sont requis s'ils sont visibles
            if (emailCard.style.display === 'block') {
                emailSubjectInput.required = true;
                emailBodyInput.required = true;
            } else { // S'ils ne sont pas visibles, s'assurer qu'ils ne bloquent pas
                emailSubjectInput.required = false;
                emailBodyInput.required = false;
            }
            createRfqForm.submit();
        });

        updateEmailOptionsVisibility(); // Appel initial
    });
</script>
@endpush
