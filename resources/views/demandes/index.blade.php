
@extends('layouts.app')

@section('title', 'Demandes Congé')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Demandes de Congé</h1>
            <button type="button" onclick="openLeaveModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                <i class="fa fa-plus mr-2"></i>
                Nouvelle demande
            </button>
        </div>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
                <strong>Succès!</strong> {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded-md mb-4">
                <strong>Erreur!</strong> {{ session('error') }}
            </div>
        @endif

        <div class="relative w-64">
            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Rechercher..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
            />
        </div>

        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach (['Nº', 'Status', 'Nom', 'Prénom', 'Date de début', 'Date de fin', 'Jours', 'Motif', 'Actions', 'Date de création'] as $header)
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $header }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($demandesConge as $demandeConge)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $demandeConge->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full
                                            @if($demandeConge->status === 'Validé') bg-green-100 text-green-800
                                            @elseif($demandeConge->status === 'Refusé') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif
                                        ">
                                            @if($demandeConge->status === 'Validé')
                                                <i class="fas fa-check"></i>
                                            @elseif($demandeConge->status === 'Refusé')
                                                <i class="fas fa-times"></i>
                                            @else
                                                <i class="fas fa-hourglass-half"></i>
                                            @endif
                                            {{ ucfirst($demandeConge->status ?? 'en Attend') }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $demandeConge->nom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $demandeConge->prénom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $demande->toDate($demandeConge->start_date)->format('d-M-Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $demande->toDate($demandeConge->end_date)->format('d-M-Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $demandeConge->nj_decompter }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $demandeConge->motif ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <form action="{{ route('demandeconge.update', $demandeConge->id) }}" method="POST" class="flex gap-1 decision-form">
                                            @csrf
                                            @method('PUT')

                                            @if(!$currentUser->isOuvrier())
                                                {{-- Accept button --}}
                                                @if(
                                                    ($demande->dmOwnerOuv($demandeConge->d_id) && (
                                                        ($currentUser->isResponsable() && $verifierDC->isAcceptedByResp($demandeConge->id)) ||
                                                        ($currentUser->isDirecteur() && $verifierDC->isAcceptedByDir($demandeConge->id)) ||
                                                        ($currentUser->isRH() && $verifierDC->isAcceptedByRH($demandeConge->id)) ||
                                                        ($currentUser->isDirecteur() && !$verifierDC->isAcceptedByResp($demandeConge->id)) ||
                                                        ($currentUser->isRH() && !$verifierDC->isAcceptedByDir($demandeConge->id)) ||
                                                        $verifierDC->isAcceptedByRH($demandeConge->id)
                                                    )) ||
                                                    ($demande->dmOwnerResp($demandeConge->d_id) && (
                                                        $currentUser->isResponsable() ||
                                                        ($currentUser->isDirecteur() && $verifierDC->isAcceptedByDir($demandeConge->id))
                                                    )) ||
                                                    ($demande->dmOwnerDir($demandeConge->d_id) && (
                                                        $currentUser->isDirecteur() && !$verifierDC->isAcceptedByRH($demandeConge->id)
                                                    )) ||
                                                    ($demande->dmOwnerrh($demandeConge->d_id) && (
                                                        $currentUser->isRH() && !$verifierDC->isAcceptedByDir($demandeConge->id)
                                                    )) ||
                                                    ($demande->areRefused($demandeConge->d_id) || $demande->areValidated($demandeConge->d_id))
                                                )
                                                    <button type="button" class="bg-green-500 text-white px-2 py-1 rounded disabled:opacity-50" disabled>
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" id="accept-btn" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                @endif

                                                {{-- Refuse button --}}
                                                <button type="button" id="refuse-btn"
                                                    class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600
                                                    @if(
                                                        $demande->areRefused($demandeConge->d_id) || $demande->areValidated($demandeConge->d_id) ||
                                                        ($demande->dmOwnerResp($demandeConge->d_id) && $currentUser->isResponsable()) ||
                                                        ($demande->dmOwnerDir($demandeConge->d_id) && $currentUser->isDirecteur()) ||
                                                        ($demande->dmOwnerRH($demandeConge->d_id) && $currentUser->isRH()) ||
                                                        ($verifierDC->isAcceptedByResp($demandeConge->id) && $currentUser->isResponsable()) ||
                                                        ($verifierDC->isAcceptedByDir($demandeConge->id) && $currentUser->isDirecteur()) ||
                                                        ($verifierDC->isAcceptedByRH($demandeConge->id) && $currentUser->isRH())
                                                    ) disabled opacity-50 @endif">
                                                    <i class="fa fa-xmark"></i>
                                                </button>
                                            @endif

                                            {{-- Download button --}}
                                            <a href="{{ route('demandeConge.downloadConge', $demandeConge->id) }}"
                                            class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600
                                                @if(!$demande->areValidated($demandeConge->d_id) || $demande->areRefused($demandeConge->d_id))
                                                    opacity-50 pointer-events-none
                                                @endif">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $demande->toDate($demandeConge->dcreated_at)->format('d-m-y h:m') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Aucune demande de congé trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- New Leave Request Modal -->
<div id="leaveModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-20">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl sm:max-w-lg max-h-[90vh] flex flex-col p-0 relative border-t-8 border-somasteel-orange" onclick="event.stopPropagation()">
        <div class="p-6 rounded-xl border-b bg-white flex justify-between items-center">
            <h3 class="text-xl font-bold text-somasteel-orange">Nouvelle demande de congé</h3>
            <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="text-center p-4 pt-0">
            <span class="text-gray-700">Solde Congé Actuelle:</span>
            <strong class="{{ $currentUser->solde_conge == 0 ? 'text-red-600' : 'text-gray-900 font-semibold' }}">
                {{ $currentUser->solde_conge . ' Jours' }}
            </strong>
        </div>

        <form id="leaveForm" action="{{ route('demandesconge.store') }}" method="POST" class="flex-1 overflow-y-auto p-6">
            @csrf
            <input type="hidden" name="matricule" value="{{ $currentUser->matricule }}">
            <input type="hidden" name="nom" value="{{ $currentUser->nom }}">
            <input type="hidden" name="prénom" value="{{ $currentUser->prénom }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="date_debut" id="start_date"
                           class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange"
                           value="{{ old('date_debut') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="date_fin" id="end_date"
                           class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange"
                           value="{{ old('date_fin') }}" required>
                </div>
            </div>

            @if ($errors->has('date_fin'))
                <div class="text-red-600 text-sm mb-4">
                    {{ $errors->first('date_fin') }}
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jours décomptés</label>
                <input type="number" name="days" id="days"
                       class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2"
                       readonly>
                <p class="mt-1 text-xs text-gray-500">(jours ouvrables)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                <textarea name="motif" rows="3"
                          class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange"
                          required placeholder="Veuillez indiquer le motif de votre demande de congé...">{{ old('motif') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Autre</label>
                <textarea name="Autre" rows="3"
                          class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange"
                          required placeholder="Autre information...">{{ old('Autre') }}</textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200 mt-6">
                <button type="reset" onclick="closeLeaveModal()"
                        class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-somasteel-orange text-white">
                    Envoyer
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Rejection Card -->
<div id="refus-card" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Raison du refus</h2>
            <button id="annuler-refus" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Raison du refus</label>
                <textarea id="raison-refus" rows="4" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                          placeholder="Veuillez indiquer la raison du refus..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button id="annuler-refus" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-gray-700">
                    Annuler
                </button>
                <button id="confirme-refus" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md text-sm font-medium text-white">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to standard date inputs
    document.getElementById('start_date').addEventListener('change', calculateDays);
    document.getElementById('end_date').addEventListener('change', calculateDays);
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});

function calculateBusinessDays(start, end) {
    let startDate = new Date(start);
    let endDate = new Date(end);
    let count = 0;

    // Loop through each date
    while (startDate <= endDate) {
        const day = startDate.getDay();
        // 0 = Sunday, 6 = Saturday
        if (day !== 0 && day !== 6) {
            count++;
        }
        // Move to next day
        startDate.setDate(startDate.getDate() + 1);
    }

    return count;
}

function calculateDays() {
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;

    if (start && end) {
        const businessDays = calculateBusinessDays(start, end);
        document.getElementById('days').value = businessDays > 0 ? businessDays : 0;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('start_date')?.addEventListener('change', calculateDays);
    document.getElementById('end_date')?.addEventListener('change', calculateDays);
});


function openLeaveModal() {
    document.getElementById('leaveModal').classList.remove('hidden');
}

function closeLeaveModal() {
    document.getElementById('leaveModal').classList.add('hidden');
}



document.querySelectorAll('#accept-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        approveLeave(this);
    });
});

document.querySelectorAll('#refuse-btn').forEach(function (button) {
    button.addEventListener('click', function () {
        rejectLeave(this);
    });
});

function approveLeave(button) {
    let decisionForm = button.closest('.decision-form'); // Find the closest form element

    // Create a hidden input field
    let hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'accepted';
    hiddenInput.value = '1';

    // Append the hidden input field to the form
    decisionForm.appendChild(hiddenInput);

    // Submit the form
    decisionForm.submit();
}

function rejectLeave(button) {
    let refusCard = document.getElementById('refus-card');

    // Remove the 'hidden' class and add fading in effect
    refusCard.classList.remove('hidden');

    // Create hidden input fields for form submission
    

    // Add event listener to the 'confirme-refus' button
    document.getElementById('confirme-refus').addEventListener('click', function () {
        let decisionForm = button.closest('.decision-form'); // Find the closest form element
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'refused';
        hiddenInput.value = 'Refusé';

        let raisonRefus = document.getElementById('raison-refus').value;
        let hiddenTextInput = document.createElement('input');
        hiddenTextInput.type = 'hidden';
        hiddenTextInput.name = 'raison_refus';
        hiddenTextInput.value = raisonRefus;

        // Append the hidden input fields to the form
        decisionForm.appendChild(hiddenInput);
        decisionForm.appendChild(hiddenTextInput);

        decisionForm.submit();

        // Hide the 'refus-card' after fading out
        setTimeout(() => {
            refusCard.classList.add('hidden');
        }, 300); // Match the duration of fadeOut animation
    });

    // Add event listener to the 'annuler-refus' button
    document.getElementById('annuler-refus').addEventListener('click', function () {
            refusCard.classList.add('hidden');
    });
}

function downloadLeave(id) {
    window.location.href = `/leaves/${id}/download`;
}
</script>

@endpush