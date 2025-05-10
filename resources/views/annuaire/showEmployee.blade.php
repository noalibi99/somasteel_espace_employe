@extends('layouts.app')

@section('title', $employee->nom . ' ' . $employee->prénom)

@section('content')
<div class="flex flex-col items-center py-8">
    <!-- Profile Card -->
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow border border-somasteel-orange flex flex-col items-center py-8 px-6 mb-6">
        @if ($employee->profile_picture)
            <img class="w-28 h-28 rounded-full object-cover border-4 border-somasteel-orange mb-4" src='{{ route('profile.image', basename($employee->profile_picture)) }}'>
        @else
            <span class="w-28 h-28 flex items-center justify-center rounded-full bg-gray-100 border-4 border-somasteel-orange mb-4">
                <i class="fa fa-user fa-3x text-gray-400"></i>
            </span>
        @endif
        <h2 class="text-2xl font-bold text-somasteel-orange mb-1">{{ $employee->nom }} {{ $employee->prénom }}</h2>
        <div class="text-gray-600 mb-2">{{ $employee->fonction }}</div>
        <div class="text-gray-500 text-sm">{{ $employee->email }}</div>
    </div>

    <!-- Details Card -->
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow border border-gray-200 px-8 py-6">
        <form id="employeeForm" action="{{ route('annuaire.employee.update', [$employee->projet, $employee->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="hidden hidden-input">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <span class="field-display">{{ $employee->nom }}</span>
                    <input type="text" name="nom" value="{{ $employee->nom }}" class="field-edit input input-bordered w-full hidden">
                </div>
                <div class="hidden hidden-input">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                    <span class="field-display">{{ $employee->prénom }}</span>
                    <input type="text" name="prénom" value="{{ $employee->prénom }}" class="field-edit input input-bordered w-full hidden">
                </div>
                <div class="hidden hidden-input">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <span class="field-display">{{ $employee->email }}</span>
                    <input type="text" name="email" value="{{ $employee->email }}" class="field-edit input input-bordered w-full hidden">
                </div>
                <div class="hidden hidden-input">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                    <span class="field-display">{{ $employee->fonction }}</span>
                    <input type="text" name="fonction" value="{{ $employee->fonction }}" class="field-edit input input-bordered w-full hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                    <span class="field-display">{{ $employee->matricule }}</span>
                    <input type="text" name="matricule" value="{{ $employee->matricule }}" class="field-edit input input-bordered w-full hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                    <span class="field-display">{{ $employee->service }}</span>
                    <select name="service" class="field-edit input input-bordered w-full hidden">
                        @foreach ($services as $service)
                            <option @if ($employee->service == $service) selected @endif value="{{$service}}">{{ $service }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <span class="field-display">
                        @if ($employee->type =='rh') Ressources Humaines
                        @elseif($employee->type =='administrateur') Admin
                        @elseif($employee->type =='directeur') Directeur
                        @elseif ($employee->type =='responsable') Responsable
                        @else Normal
                        @endif
                    </span>
                    <select name="type" class="field-edit input input-bordered w-full hidden">
                        <option value="ouvrier" @if ($employee->type =='ouvrier') selected @endif>Normal</option>
                        <option value="responsable" @if ($employee->type =='responsable') selected @endif>Responsable</option>
                        <option value="directeur" @if ($employee->type =='directeur') selected @endif>Directeur</option>
                        <option value="rh" @if ($employee->type =='rh') selected @endif>Ressources Humaines</option>
                        <option value="administrateur" @if ($employee->type =='administrateur') selected @endif>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Solde Congé</label>
                    <span class="field-display">{{ $employee->solde_conge }}</span>
                    <input type="number" name="solde_conge" value="{{ $employee->solde_conge }}" class="field-edit input input-bordered w-full hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable Hiérarchique</label>
                    <span class="field-display">{{ $employee->responsable_hiarchique }}</span>
                    <input type="text" name="responsable_hiarchique" value="{{ $employee->responsable_hiarchique }}" class="field-edit input input-bordered w-full hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Directeur</label>
                    <span class="field-display">{{ $employee->directeur }}</span>
                    <input type="text" name="directeur" value="{{ $employee->directeur }}" class="field-edit input input-bordered w-full hidden">
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-8">
                <button type="submit" id="save-button" class="field-edit px-4 py-2 rounded-lg bg-somasteel-orange text-white hidden">
                    <i class="fa fa-check mr-1"></i> Enregistrer
                </button>
                <button type="button" id="cancel-button" class="field-edit px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 hidden">
                    <i class="fa fa-times mr-1"></i> Annuler
                </button>
                <button type="button" id="edit-button" class="field-display px-4 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600">
                    <i class="fa fa-pencil mr-1"></i> Modifier
                </button>
                <button type="button" class="field-display px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600"
                        onclick="openDeleteEmployeeModal('{{ $employee->id }}', '{{ $employee->nom }} {{ $employee->prénom }}')">
                    <i class="fa fa-trash mr-1"></i> Supprimer
                </button>
                <button type="button" class="field-display px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 ml-auto"
                        onclick="openChangePasswordModal()">
                    <i class="fa fa-lock mr-1"></i> Changer Mot de Passe
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-10 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8 border-t-8 border-somasteel-orange">
        <h3 class="text-xl font-bold mb-4 text-somasteel-orange">Changer le mot de passe</h3>
        <form id="changePasswordForm" action="{{ route('annuaire.employee.changePassword', $employee->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" id="newPassword" name="new_password" class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-somasteel-orange" required minlength="8">
                    <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer" onclick="togglePasswordVisibility('newPassword', 'confirmPassword', this)">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <div class="text-red-600 text-xs mt-1 hidden" id="newPasswordError">Le mot de passe est requis et doit contenir au moins 8 caractères.</div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation du mot de passe</label>
                <input type="password" id="confirmPassword" name="confirm_new_password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-somasteel-orange" required minlength="8">
                <div class="text-red-600 text-xs mt-1 hidden" id="confirmPasswordError">La confirmation du mot de passe est incorrecte.</div>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200" onclick="closeChangePasswordModal()">Annuler</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-somasteel-orange text-white">Changer le mot de passe</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Edit mode toggle
    document.getElementById('edit-button').addEventListener('click', function () {
        toggleEditMode(true);
    });
    document.getElementById('cancel-button').addEventListener('click', function () {
        toggleEditMode(false);
    });

    // If URL contains ?edit, enter edit mode
    if (window.location.search.includes('edit')) {
        toggleEditMode(true);
    }

    function toggleEditMode(editMode) {
        document.querySelectorAll('.field-display').forEach(el => el.classList.toggle('hidden', editMode));
        document.querySelectorAll('.field-edit').forEach(el => el.classList.toggle('hidden', !editMode));
        document.getElementById('edit-button').classList.toggle('hidden', editMode);
        document.getElementById('save-button').classList.toggle('hidden', !editMode);
        document.getElementById('cancel-button').classList.toggle('hidden', !editMode);
        document.querySelectorAll('div.hidden-input').forEach(el => el.classList.toggle('hidden', !editMode));
    }

    // Delete modal
    window.openDeleteEmployeeModal = function(id, name) {
        document.getElementById('employeeNameToDelete').textContent = name;
        document.getElementById('deleteEmployeeForm').action = '/Annuaire/delete/' + id;
        document.getElementById('deleteEmployeeModal').classList.remove('hidden');
    }
    window.closeDeleteEmployeeModal = function() {
        document.getElementById('deleteEmployeeModal').classList.add('hidden');
    }

    // Change password modal
    window.openChangePasswordModal = function() {
        document.getElementById('changePasswordModal').classList.remove('hidden');
    }
    window.closeChangePasswordModal = function() {
        document.getElementById('changePasswordModal').classList.add('hidden');
    }

    // Password visibility toggle
    window.togglePasswordVisibility = function(passwordId, confirmId, el) {
        const passwordField = document.getElementById(passwordId);
        const confirmField = document.getElementById(confirmId);
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        confirmField.type = type;
        el.querySelector('i').classList.toggle('fa-eye');
        el.querySelector('i').classList.toggle('fa-eye-slash');
    }
});
</script>
@endsection