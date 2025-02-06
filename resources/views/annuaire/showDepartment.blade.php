@extends('layouts.app')

@push('vite')
    @vite(['resources/js/showDepart.js',])
@endpush

@section('content')
<style>
.employee-table th,
.employee-table td {
    vertical-align: middle;
}
</style>
<div class="container mt-4">
    <div class="row my-3">
        <h3 class="col-lg-8 col-md-8 col-sm-12">
            <a href="{{ route('annuaire.index') }}" class="nav-link d-inline">
                <button class="btn btn-sm btn-secondary me-2"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
            </a>
                <b class="border-bottom border-black border-2"><em> <span class="no-break">{{__('Annuaire Employés')}}</span> {{__(' - ' . $projet . ' - ' . $depart)}}</em></b>
        </h3>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <button class="btn btn-sm btn-warning no-break d-inline float-end" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                <i class="fa fa-plus me-2"></i> {{__('Crée nouveau compte')}}
            </button>
        </div>
    </div>
    
    
    <div class="table-responsive">
        {{-- <div class=" resp-form">
            <form action="{{ route('department.updateResponsible', 3) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="input-group input-group-sm">
                    <input type="text" name="responsible" class="form-control" placeholder="{{ __('Nom du responsable') }}" value="">
                    <button class="btn btn-warning" type="submit">{{ __('Définir responsable') }}</button>
                </div>
            </form>
        </div> --}}
        <table class="table table-striped employee-table">
            <thead>
                <tr>
                    <th scope="col">Photo</th>
                    <th scope="col">Nom Prénom</th>
                    <th scope="col">Matricule</th>
                    <th scope="col">Fonction</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(!$employees->isEmpty())
                    @foreach($employees as $employee)
                    <tr>
                        <td>
                            @if ($employee->profile_picture)
                                <img 
                                class="img-fluid rounded"
                                src='{{ route('profile.image', basename($employee->profile_picture)) }}'
                                >
                            @else
                                <i class="fa fa-user fa-2x"></i>
                            @endif</td>
                        <td class="no-break">{{ $employee->nom . " " . $employee->prénom }}</td>
                        <td>{{ $employee->matricule }}</td>
                        <td class="no-break">{{ $employee->fonction }}</td>
                        <td class="no-break">
                            <div class="btn-group">
                                {{-- @dd($employee->projet) --}}
                                <a href="{{ route('annuaire.employee', [
                                                    'projet'=> $employee->projet,
                                                    'depart' => $employee->depart,
                                                    'employee_nom' => $employee->nom,
                                                    'employee_id' => $employee->id
                                    ])  }}" class="btn btn-info"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <form action="{{ route('annuaire.employee.setResponsable', [
                                    'id' => $employee->id,
                                    'depart' => $employee->depart,
                                    'projet' => $employee->projet
                                ]) }}" method="POST" style="display:inline;">
                                    @method('PUT')
                                    @csrf
                                    <button type="submit" class="btn @if($employee->type == 'responsable') btn-primary @else btn-warning @endif">
                                        <i class="fa-solid fa-registered"></i>
                                    </button>
                                </form>
                                

                                <a href="{{ route('annuaire.employee', [
                                                    'projet'=> $employee->projet,
                                                    'depart' => $employee->depart,
                                                    'employee_nom' => $employee->nom,
                                                    'employee_id' => $employee->id
                                    ]) . "?edit=true"}}" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-employee-id="{{ $employee->id }}" data-employee-name="{{ $employee->nom }} {{ $employee->prénom }}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5">Aucun employé trouvé dans ce département.</td>
                    </tr>
                    @endif
            </tbody>
        </table>
    </div>
    {{-- destroy confirmation modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmation de Suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer l'employé <b id="employeeNameToDelete"></b>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- create modle --}}
    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="createEmployeeModalLabel">Ajouter un nouvel employé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createEmployeeForm" action="{{ route('annuaire.employee.register', [$projet, $depart]) }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3 form-floating">
                                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" value="{{ old('nom') }}" required>
                                    <label for="nom">Nom</label>
                                    <div class="invalid-feedback">Nom est requis.</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3 form-floating">
                                    <input type="text" class="form-control" id="prénom" name="prénom" placeholder="Prénom" value="{{ old('prénom') }}" required>
                                    <label for="prénom">Prénom</label>
                                    <div class="invalid-feedback">Prénom est requis.</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 form-floating">
                            <input type="email" class="form-control form-control-sm" id="email" name="email" value="{{ old('email') }}" placeholder="Email (example@exmp.com)" aria-label=".form-control-sm example" required>
                            <label for="email">Email (example@exmp.com)</label>
                            <div class="invalid-feedback">Entrez un email valide.</div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3 form-floating">
                                    <input type="text" class="form-control form-control-md" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Matricule" required>
                                    <label for="matricule">Matricule</label>
                                    <div class="invalid-feedback">Matricule est requis.</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3 form-floating">
                                    <select class="form-select form-select-md" id="type" name="type" value="{{ old('type') }}" required>
                                        <option value="" disabled selected>Choisir le type</option>
                                        <option value="ouvrier">Normal</option>
                                        <option value="responsable">Responsable</option>
                                        <option value="directeur">Directeur</option>
                                        <option value="rh">Ressources Humaines</option>
                                        <option value="administrateur">Admin</option>
                                    </select>
                                    <label for="type">Type</label>
                                    <div class="invalid-feedback">Type est requis (Normal, Responsable, Directeur, Ressources Humaines).</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="mb-3 form-floating"> 
                                    <input type="text" class="form-control" id="fonction" name="fonction" value="{{ old('fonction') }}" placeholder="Fonction">
                                    <label for="fonction">Fonction</label>
                                </div>
                                <input type="text" class="form-control" hidden id="service" name="service" placeholder="Service" value="{{ $depart }}">
                                <input type="text" class="form-control" hidden id="projet" name="projet" placeholder="Projet" value="{{ $projet }}">
                            </div>
                            <div class="col">
                                <div class="mb-3 form-floating">
                                    <input type="number" class="form-control" id="solde_conge" name="solde_conge" value="{{ old('solde_conge') }}" placeholder="Solde Congé">
                                    <label for="solde_conge">Solde Congé actuelle</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8 me-0 pe-1">
                                <div class="mb-3 form-floating">
                                    <input type="text" class="form-control" id="responsable_hiarchique" value="{{ old('responsable_hiarchique') }}" placeholder="Responsable Hiérarchique">
                                    <label for="responsable_hiarchique">Responsable Hiérarchique</label>
                                </div>
                            </div>
                            <div class="col-4 ms-0 ps-1 form-floating text-center">
                                <input type="text" name="responsable_hiarchique" id="responsable_hiarchique_matricule" value="{{ null }}" readonly class="field-edit text-center bg-secondary-subtle form-control">
                                <label for="responsable_hiarchique_matricule" class="text-center bg-transparent">Matricule</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8 me-0 pe-1">
                                <div class="mb-3 form-floating">
                                    <input type="text" class="form-control" id="directeur" value="{{ old('directeur') }}" placeholder="Directeur">
                                    <label for="directeur">Directeur</label>
                                </div>
                            </div>
                            <div class="col-4 ms-0 ps-1 form-floating text-center">
                                <input type="text" name="directeur" id="directeur_matricule" value="{{ null }}" readonly class="field-edit text-center bg-secondary-subtle form-control">
                                <label for="directeur_matricule" class="text-center bg-transparent">Matricule</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Mot de passe" required>
                                <label for="password">Mot de passe</label>
                                <div class="invalid-feedback">Le mot de passe est requis et doit contenir au moins 8 caractères.</div>
                            </div>
                            <button type="button" class="input-group-text eye-icon" id="togglePasswordVisibility">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        
                        <div class="mb-3 form-floating">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Confirmation du mot de passe" required>
                            <label for="password_confirmation">Confirmation du mot de passe</label>
                            <div class="invalid-feedback">La confirmation du mot de passe est incorrect.</div>
                        </div>
                    </div>
                    <style>
                        .ui-autocomplete {
                            z-index: 999999 !important;
                        }
                    </style>
                    <script>
                        var responsables = {!! json_encode($responsables) !!};
                        var directeurs = {!! json_encode($directeurs) !!};
                    </script>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection