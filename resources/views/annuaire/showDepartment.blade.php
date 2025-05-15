@extends('layouts.app')

@section('title', $projet . ' - ' . $depart)

@section('content')
<div class="min-h-screen bg-white flex flex-col px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div class="flex items-center space-x-2">
            <a href="{{ route('annuaire.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-black">
                <i class="fa fa-angle-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-black ml-4">
                Annuaire Employés - {{ $projet }} - {{ $depart }}
            </h1>
        </div>
        <div class="mt-4 md:mt-0">
            <button type="button" onclick="showCreateEmployeeModal()" class="inline-flex items-center px-4 py-2 rounded-lg bg-somasteel-orange text-white hover:bg-somasteel-orange/90 font-semibold shadow transition">
                <i class="fa fa-plus mr-2"></i> Créer nouveau employé
            </button>
        </div>
    </div>

    <!-- Employee Table -->
    <!-- Employee Table (Demandes Congé Style) -->
<div class="flex flex-col mt-8">
    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom Prénom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($employees as $employee)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if ($employee->profile_picture)
                                    <img src='{{ route('profile.image', basename($employee->profile_picture)) }}'
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="Photo">
                                @else
                                    <i class="fa fa-user fa-2x text-gray-400"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $employee->nom . " " . $employee->prénom }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $employee->matricule }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $employee->fonction }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex gap-1">
                                    <!-- View -->
                                    <a href="{{ route('annuaire.employee', [
                                            'projet'=> $employee->projet,
                                            'depart' => $employee->depart,
                                            'employee_nom' => $employee->nom,
                                            'employee_id' => $employee->id
                                        ])  }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-somasteel-orange/20 text-somasteel-orange"
                                       title="Voir">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <!-- Set Responsable -->
                                    <form action="{{ route('annuaire.employee.setResponsable', [
                                            'id' => $employee->id,
                                            'depart' => $employee->depart,
                                            'projet' => $employee->projet
                                        ]) }}" method="POST" class="inline">
                                        @method('PUT')
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                                       {{ $employee->type == 'responsable' ? 'bg-somasteel-orange text-white' : 'bg-gray-100 text-somasteel-orange hover:bg-somasteel-orange/20' }}"
                                                title="Définir responsable">
                                            <i class="fa-solid fa-registered"></i>
                                        </button>
                                    </form>
                                    <!-- Edit -->
                                    <a href="{{ route('annuaire.employee', [
                                            'projet'=> $employee->projet,
                                            'depart' => $employee->depart,
                                            'employee_nom' => $employee->nom,
                                            'employee_id' => $employee->id
                                        ]) . "?edit=true"}}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-somasteel-orange/20 text-green-600"
                                       title="Éditer">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <!-- Delete -->
                                    <button type="button"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-red-100 text-red-600"
                                            title="Supprimer"
                                            onclick="openDeleteEmployeeModal('{{ $employee->id }}', '{{ $employee->nom }} {{ $employee->prénom }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun employé trouvé dans ce département.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
    <div class="pagination">
        {{ $employees->links('pagination::somasteel') }}
    </div>
</div>

<!-- Create Employee Modal -->
<div id="CreateEmployeeModal" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-20 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl sm:max-w-lg max-h-[90vh] flex flex-col p-0 relative border-t-8 border-somasteel-orange">
            <div class="p-6 rounded-xl border-b bg-white">
                <h3 class="text-xl font-bold text-somasteel-orange">Ajouter un nouvel employé</h3>
            </div>
            <form id="createEmployeeForm" action="{{ route('annuaire.employee.register', [$projet, $depart]) }}" method="POST" autocomplete="off" class="flex-1 overflow-y-auto p-6">
                @csrf

                <input type="hidden" name="service" id="user-service-name" value="{{ $depart }}" required>
                <input type="hidden" name="projet" id="create-emp-projet" value="{{ $projet }}" required>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="nom" name="nom" placeholder="Nom" required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="nomError">Nom est requis.</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="prénom" name="prénom" placeholder="Prénom" required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="prenomError">Prénom est requis.</div>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="email" name="email" placeholder="Email (example@exmp.com)">
                    <div class="text-red-600 text-xs mt-1 hidden" id="emailError">Entrez un email valide.</div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Matricule</label>
                        <input type="text" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="matricule" name="matricule" placeholder="Matricule" required>
                        <div class="text-red-600 text-xs mt-1 hidden" id="matriculeError">Matricule est requis.</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="type" name="type" required>
                            <option value="ouvrier">Normal</option>
                            <option value="responsable" selected>Responsable</option>
                            <option value="directeur">Directeur</option>
                            <option value="rh">Ressources Humaines</option>
                            <option value="administrateur">Admin</option>
                        </select>
                        <div class="text-red-600 text-xs mt-1 hidden" id="typeError">Type est requis.</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fonction</label>
                        <input type="text" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="fonction" name="fonction" placeholder="Fonction">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Solde Congé actuelle</label>
                        <input type="number" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="solde_conge" name="solde_conge" placeholder="Solde Congé">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Responsable Hiérarchique</label>
                        <input type="text" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="responsable_hiarchique" name="responsable_hiarchique" placeholder="Responsable Hiérarchique">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Matricule</label>
                        <input type="text" readonly class="mt-1 block w-full border  bg-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="responsable_hiarchique_matricule" name="responsable_hiarchique_matricule" placeholder="Matricule">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Directeur</label>
                        <input type="text" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="directeur" name="directeur" placeholder="Directeur">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Matricule</label>
                        <input type="text" readonly class="mt-1 block w-full border  bg-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="directeur_matricule" name="directeur_matricule" placeholder="Matricule">
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <div class="relative w-full">
                        <input 
                            type="password" 
                            class="mt-1 block w-full border  rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange"
                            id="password" name="password" placeholder="Mot de passe" required>
                        <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer" onclick="togglePasswordVisibility('password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <div class="text-red-600 text-xs mt-1 hidden" id="passwordError">Le mot de passe est requis et doit contenir au moins 8 caractères.</div>
                    </div>
                </div>
                <div class="mt-4">
                    <input 
                        type="password" 
                        class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange"
                        id="password_confirmation" name="password_confirmation" placeholder="Confirmation du mot de passe" required>
                    <div class="text-red-600 text-xs mt-1 hidden" id="passwordConfirmError">La confirmation du mot de passe est incorrect.</div>
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="hideCreateEmployeeModal()" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">Annuler</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-somasteel-orange text-white">Créer</button>
                </div>
            </form>
        </div>
</div>

    <!-- Delete Employee Modal (hidden by default, show with JS) -->
    <div id="deleteEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-10 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8 border-t-8 border-somasteel-orange">
            <h3 class="text-xl font-bold mb-4 text-somasteel-orange">Confirmation de Suppression</h3>
            <p class="mb-6 text-gray-700">Êtes-vous sûr de vouloir supprimer l'employé <b id="employeeNameToDelete"></b> ?</p>
            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" onclick="closeDeleteEmployeeModal()">Annuler</button>
                <form id="deleteEmployeeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-somasteel-orange text-white">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteEmployeeModal(id, name) {
    document.getElementById('employeeNameToDelete').textContent = name;
    document.getElementById('deleteEmployeeForm').action = '/Annuaire/delete/' + id;
    document.getElementById('deleteEmployeeModal').classList.remove('hidden');
}
function closeDeleteEmployeeModal() {
    document.getElementById('deleteEmployeeModal').classList.add('hidden');
}
    // Show the modal
    function showCreateEmployeeModal() {
        document.getElementById('CreateEmployeeModal').classList.remove('hidden');
    }

    // Hide and reset the modal
    function hideCreateEmployeeModal() {
        const modal = document.getElementById('CreateEmployeeModal');
        const form = document.getElementById('createEmployeeForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();

        // Optional: Hide validation errors if any were shown
        const errors = form.querySelectorAll('.text-red-600');
        errors.forEach(err => err.classList.add('hidden'));
    }

    // Attach click event to cancel button
    document.getElementById('cancelAddDeptStep2').addEventListener('click', hideCreateEmployeeModal);
</script>
@endsection