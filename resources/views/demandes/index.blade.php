
@extends('layouts.app')

@section('title', 'Demandes Congé')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Demandes de Congé</h1>
            <button type="button" onclick="openLeaveModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                Nouvelle demande
            </button>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
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
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($demandeConge->status === 'Valider') bg-green-100 text-green-800
                                            @elseif($demandeConge->status === 'refusé') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif
                                        ">
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
                                        <form action="{{ route('demandeconge.update', $demandeConge->id) }}" method="POST" class="flex gap-1">
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
                                                    <button type="submit" name="decision" value="accept" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                @endif

                                                {{-- Refuse button --}}
                                                <button type="submit" name="decision" value="refuse"
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
<div id="leaveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center border-b border-gray-200 p-4">
            <h2 class="text-xl font-semibold text-gray-800">Nouvelle demande de congé</h2>
            <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="text-center">
            <span class="text-gray-700">{{ __("Solde Congé Actuelle:") }}</span>
            <strong class="{{ $currentUser->solde_conge == 0 ? 'text-red-600' : 'text-gray-900 font-semibold' }}">
                {{ $currentUser->solde_conge . __(' Jours') }}
            </strong>
        </div>
        <form id="leaveForm" action="{{ route('demandesconge.store') }}" method="POST" class="p-4">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input value="{{$currentUser->nom}}" @readonly(true) required type="text" class="w-full p-2 border rounded-md"
                        name="nom" placeholder="n" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <input value="{{$currentUser->prénom}}" @readonly(true) required type="text"
                        class="w-full p-2 border rounded-md" name="prénom" placeholder="n" />
                </div>
            </div>
            <div class="grid grid-cols-1 mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                <input required type="number" class="w-full p-2 border rounded-md" name="matricule" @readonly(true)
                    placeholder="n" value="{{auth()->user()->matricule}}" />
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="date_debut" id="start_date" class="w-full p-2 border rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="date_fin" id="end_date" class="w-full p-2 border rounded-md" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jours décomptés</label>
                <input type="number" name="days" id="days" class="w-full p-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                <p class="mt-1 text-xs text-gray-500">(jours ouvrables)</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                <textarea name="motif" rows="3" class="w-full p-2 border rounded-md" required
                          placeholder="Veuillez indiquer le motif de votre demande de congé..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="reset" onclick="closeLeaveModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 border border-transparent rounded-md text-sm font-medium text-white"
                        @if(auth()->user()->solde_conge <= 0 || !$currentUser->hasDemandes()) @disabled(true) @endif>
                    Soumettre
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();

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

function approveLeave(id) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette demande ?')) {
        fetch(`/leaves/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => window.location.reload());
    }
}

function rejectLeave(id) {
    if (confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')) {
        fetch(`/leaves/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => window.location.reload());
    }
}

function downloadLeave(id) {
    window.location.href = `/leaves/${id}/download`;
}
</script>

@endpush