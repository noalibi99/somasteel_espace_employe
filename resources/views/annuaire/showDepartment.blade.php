@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white flex flex-col px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div class="flex items-center space-x-2">
            <a href="{{ route('annuaire.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-black">
                <i class="fa fa-angle-left mr-2"></i> Retour
            </a>
            <h1 class="text-2xl font-bold text-black ml-4">
                Annuaire Employés - {{ $projet }} - {{ $depart }}
            </h1>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('annuaire.employee.register', [$projet, $depart]) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-somasteel-orange text-white hover:bg-somasteel-orange/90 font-semibold shadow transition">
                <i class="fa fa-plus mr-2"></i> Créer nouveau compte
            </a>
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
</script>
@endsection