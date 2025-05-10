@extends('layouts.app')

@section('title', 'Annuaires des employés')

@section('content')
<div class="min-h-screen bg-white flex flex-col">
    <!-- Header -->
    <header class="flex items-center justify-between px-8 py-6 bg-white shadow-sm">
        <h1 class="text-2xl font-bold text-gray-900">Annuaires des employés</h1>
        <div class="relative w-96">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fa-solid fa-magnifying-glass text-lg text-black"></i>
            </span>
            <input 
                id="searchInput"
                type="text" 
                placeholder="Rechercher un département..." 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out">
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 px-8 py-8 bg-white">
        <div id="projectsContainer" class="space-y-10">
            @foreach($projects as $project => $departments)
                <section class="border border-somasteel-orange rounded-2xl shadow bg-white">
                    <div class="flex items-center justify-between px-6 py-4 bg-somasteel-orange/10 rounded-t-2xl">
                        <h2 class="text-lg font-bold flex items-center text-black">
                            <i class="fa-solid fa-building text-2xl mr-2 text-black"></i>
                            {{ $project }}
                        </h2>
                        <button type="button" class="toggle-btn p-2 rounded-full hover:bg-somasteel-orange/20 focus:outline-none" data-project="{{ Str::slug($project) }}">
                            <i class="fa-solid fa-chevron-right text-2xl transition-transform text-somasteel-orange"></i>
                        </button>
                    </div>
                    <div class="departments-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 px-6 py-6" id="departments-{{ Str::slug($project) }}">
                        @foreach($departments as $department)
                            <div class="relative rounded-2xl shadow-md border border-somasteel-orange p-6 flex flex-col bg-white department-card" data-department="{{ strtolower($department) }}">
                                <div class="flex items-center mb-3">
                                    <i class="fa-solid fa-building text-2xl mr-3 text-somasteel-orange"></i>
                                    <h3 class="text-lg font-bold text-gray-800 flex-1">{{ $department }}</h3>
                                    <!-- Dropdown -->
                                    <div class="relative">
                                        <button type="button" class="action-dropdown-btn p-2 rounded-full hover:bg-somasteel-orange/20 focus:outline-none" data-dropdown="{{ Str::slug($project) . '-' . Str::slug($department) }}">
                                            <i class="fa-solid fa-ellipsis-vertical text-xl text-gray-500"></i>
                                        </button>
                                        <div class="dropdown-menu absolute right-0 mt-2 w-32 bg-white border border-somasteel-orange rounded-lg shadow-lg z-50 hidden" id="dropdown-{{ Str::slug($project) . '-' . Str::slug($department) }}">
                                        <a href="{{ route('annuaire.depart', ['projet' => $project, 'depart' => $department]) }}"
   class="block px-4 py-2 text-gray-700 hover:bg-somasteel-orange/10">
   Voir
</a>
                                            <button type="button" class="w-full text-left px-4 py-2 text-red-600 hover:bg-somasteel-orange/10" onclick="openDeleteDeptModal('{{ addslashes($project) }}', '{{ addslashes($department) }}')">Supprimer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!-- Add Department Card -->
                        <button type="button" class="flex flex-col items-center justify-center border-2 border-dashed border-somasteel-orange bg-somasteel-orange/10 hover:bg-somasteel-orange/20 rounded-2xl p-6 transition" onclick="openAddDeptModal('{{ addslashes($project) }}')">
                            <i class="fa-solid fa-plus text-3xl mb-2 text-black"></i>
                            <span class="font-semibold text-black">Ajouter un département</span>
                        </button>
                    </div>
                </section>
            @endforeach
        </div>
    </main>

    <!-- Step 1: Department Name Modal -->
    <div id="addDeptModalStep1" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-20 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8 relative border-t-8 border-somasteel-orange">
            <h3 class="text-xl font-bold mb-4 text-somasteel-orange">Ajouter un département</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nom du département</label>
                <input type="text" id="deptNameStep1" class="mt-1 block w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" required>
                <div class="text-red-600 text-xs mt-1 hidden" id="deptNameStep1Error">Le nom du département est requis.</div>
            </div>
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" id="cancelAddDeptStep1" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">Annuler</button>
                <button type="button" id="nextAddDeptStep1" class="px-4 py-2 rounded-lg bg-somasteel-orange text-white">Suivant</button>
            </div>
        </div>
    </div>

    <!-- Step 2: User Creation Modal (Scrollable & Responsive) -->
    <div id="addDeptModalStep2" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-20 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl sm:max-w-lg max-h-[90vh] flex flex-col p-0 relative border-t-8 border-somasteel-orange">
            <div class="p-6 rounded-xl border-b bg-white">
                <h3 class="text-xl font-bold text-somasteel-orange">Ajouter un nouvel employé</h3>
            </div>
            <form id="createEmployeeForm" action="{{ route('annuaire.depart.store') }}" method="POST" autocomplete="off" class="flex-1 overflow-y-auto p-6">
                @csrf
                <input type="hidden" name="service" id="user-service-name">
                <input type="hidden" name="projet" id="create-emp-projet">

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
                    <input type="email" class="mt-1 block w-full border  rounded-lg px-3 py-2 focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange" id="email" name="email" placeholder="Email (example@exmp.com)" required>
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
                    <button type="button" id="cancelAddDeptStep2" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">Annuler</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-somasteel-orange text-white">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Department Modal (unchanged) -->
    <div id="deleteDeptModal" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-10 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8 relative border-t-8 border-somasteel-orange">
            <h3 class="text-xl font-bold mb-4 text-somasteel-orange">Supprimer le département</h3>
            <h3 class="mb-6 text-gray-700">Tous les comptes dans ce service seront supprimés</h4>
            <p class="mb-6 text-gray-700">Êtes-vous sûr de vouloir supprimer le département <span id="deleteDeptName" class="font-bold text-somasteel-orange"></span> ? <b> Cette action est irréversible. </b></p>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelDeleteDept" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200">Annuler</button>
                <form id="deleteDeptForm" method="POST" action="{{ route('annuaire.depart.delete') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="project" id="deleteDeptProject">
                    <input type="hidden" name="nomService" id="deleteDeptService">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-somasteel-orange text-white">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
/* --- Expand/Collapse Projects (all shown by default, user can toggle) --- */
document.querySelectorAll('.toggle-btn').forEach(btn => {
    const projectSlug = btn.getAttribute('data-project');
    const grid = document.getElementById('departments-' + projectSlug);
    const icon = btn.querySelector('i');
    // Show all by default
    grid.style.display = '';
    icon.classList.add('rotate-90');

    btn.addEventListener('click', function() {
        if (grid.style.display === '' || grid.style.display === 'block') {
            grid.style.display = 'none';
            icon.classList.remove('rotate-90');
        } else {
            grid.style.display = '';
            icon.classList.add('rotate-90');
        }
    });
});

/* --- Dropdown Actions --- */
document.querySelectorAll('.action-dropdown-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdownId = this.getAttribute('data-dropdown');
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu.id === 'dropdown-' + dropdownId) {
                menu.classList.toggle('hidden');
            } else {
                menu.classList.add('hidden');
            }
        });
    });
});
document.addEventListener('click', function() {
    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
});

/* --- Search Departments --- */
document.getElementById('searchInput').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.departments-grid').forEach(grid => {
        let hasVisible = false;
        grid.querySelectorAll('.department-card').forEach(card => {
            const dept = card.getAttribute('data-department');
            if (dept.includes(query)) {
                card.style.display = '';
                hasVisible = true;
            } else {
                card.style.display = 'none';
            }
        });
        // Show/hide the grid based on search
        if (query && hasVisible) {
            grid.style.display = '';
            // Also update the toggle button to "open"
            const section = grid.closest('section');
            const btn = section.querySelector('.toggle-btn');
            btn.querySelector('i').classList.add('rotate-90');
        } else if (query && !hasVisible) {
            grid.style.display = 'none';
        } else {
            grid.style.display = '';
            // Reset toggle button
            const section = grid.closest('section');
            const btn = section.querySelector('.toggle-btn');
            btn.querySelector('i').classList.add('rotate-90');
        }
    });
});

/* --- Add Department Modal: Step 1 --- */
function openAddDeptModal(project) {
    document.getElementById('addDeptModalStep1').classList.remove('hidden');
    document.getElementById('create-emp-projet').value = project;
    document.getElementById('deptNameStep1').value = '';
    document.getElementById('deptNameStep1Error').classList.add('hidden');
    // Reset Step 2 fields
    document.getElementById('user-service-name').value = '';
    document.getElementById('nom').value = '';
    document.getElementById('prénom').value = '';
    document.getElementById('email').value = '';
    document.getElementById('matricule').value = '';
    document.getElementById('type').value = 'responsable';
    document.getElementById('fonction').value = '';
    document.getElementById('solde_conge').value = '';
    document.getElementById('responsable_hiarchique').value = '';
    document.getElementById('responsable_hiarchique_matricule').value = '';
    document.getElementById('directeur').value = '';
    document.getElementById('directeur_matricule').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password_confirmation').value = '';
    document.getElementById('passwordError').classList.add('hidden');
    document.getElementById('passwordConfirmError').classList.add('hidden');
    document.getElementById('password').classList.remove('border-red-500');
    document.getElementById('password_confirmation').classList.remove('border-red-500');
}

// Cancel in Step 1
document.getElementById('cancelAddDeptStep1').addEventListener('click', function() {
    document.getElementById('addDeptModalStep1').classList.add('hidden');
});

// Next in Step 1
document.getElementById('nextAddDeptStep1').addEventListener('click', function() {
    const deptName = document.getElementById('deptNameStep1').value.trim();
    if (!deptName) {
        document.getElementById('deptNameStep1Error').classList.remove('hidden');
        document.getElementById('deptNameStep1').classList.add('border-red-500');
        return;
    }
    document.getElementById('deptNameStep1Error').classList.add('hidden');
    document.getElementById('deptNameStep1').classList.remove('border-red-500');
    document.getElementById('addDeptModalStep1').classList.add('hidden');
    document.getElementById('addDeptModalStep2').classList.remove('hidden');
    document.getElementById('user-service-name').value = deptName;
});

// Cancel in Step 2
document.getElementById('cancelAddDeptStep2').addEventListener('click', function() {
    document.getElementById('addDeptModalStep2').classList.add('hidden');
});

/* --- Delete Department Modal --- */
function openDeleteDeptModal(project, department) {
    document.getElementById('deleteDeptModal').classList.remove('hidden');
    document.getElementById('deleteDeptName').textContent = department;
    document.getElementById('deleteDeptProject').value = project;
    document.getElementById('deleteDeptService').value = department;
}
document.getElementById('cancelDeleteDept').addEventListener('click', function() {
    document.getElementById('deleteDeptModal').classList.add('hidden');
});

/* --- Password visibility toggle --- */
function togglePasswordVisibility(inputId, iconSpan) {
    const input = document.getElementById(inputId);
    const icon = iconSpan.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

/* --- Password validation on submit --- */
document.getElementById('createEmployeeForm').addEventListener('submit', function(e) {
    let valid = true;
    // Password
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const passwordError = document.getElementById('passwordError');
    const passwordConfirmError = document.getElementById('passwordConfirmError');
    if (!password || password.length < 8) {
        passwordError.classList.remove('hidden');
        document.getElementById('password').classList.add('border-red-500');
        valid = false;
    } else {
        passwordError.classList.add('hidden');
        document.getElementById('password').classList.remove('border-red-500');
    }
    if (password !== confirm) {
        passwordConfirmError.classList.remove('hidden');
        document.getElementById('password_confirmation').classList.add('border-red-500');
        valid = false;
    } else {
        passwordConfirmError.classList.add('hidden');
        document.getElementById('password_confirmation').classList.remove('border-red-500');
    }
    if (!valid) e.preventDefault();
});
</script>
@endsection