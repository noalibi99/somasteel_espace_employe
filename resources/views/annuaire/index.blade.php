@extends('layouts.app')

@push('vite')
    @vite(['resources/js/department.js'])
@endpush

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between my-3">
        <h2 class="mb-2">
            <b class="border-bottom border-black border-2 no-break"><em>{{ __('Annuaire Département') }}</em></b>
        </h2>
        <input type="text" id="departmentFilter" class="form-control mx-1 mb-2" placeholder="Search departments...">
    </div>
    <div id="projectsContainer">
        @foreach ($projects as $project => $departments)
            <div class="project-section">
                <a class="nav-link" data-bs-toggle="collapse" href="#{{ 'id_' . __($project )}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <h3>{{ __($project) }} <i class="fas fa-sort-down projet-icon rotate-icon"></i></h3>
                </a>

                <div class="collapse show" id="{{ 'id_' . __($project)}}">
                    <div class="row departments">
                        @foreach ($departments as $department)
                        <div class="col-lg-3 col-md-4 col-sm-6 department-item" data-service="{{ $department }}">
                            <div class="card department-card">
                                <!-- Dropdown button for the card -->
                                <div class="dropdown position-absolute top-0 end-0 p-2" >
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end bg-light" aria-labelledby="dropdownMenuButton">
                                        <li><a class="dropdown-item" href="{{ route('annuaire.depart', [$project, $department]) }}">Voir</a></li>
                                        <hr class="my-0">
                                        <li><a class="dropdown-item delete-department-btn"
                                                data-project="{{ $project }}"
                                                data-department="{{ $department }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteDepartmentModal">
                                                Supprimer
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                        
                                <a class="nav-link" href="{{ route('annuaire.depart', [$project, $department]) }}">
                                    <div class="card-body text-center">
                                        <i class="fas fa-building fa-3x mb-3"></i>
                                        <h5 class="card-title">{{ __($department) }}</h5>
                                    </div>
                                </a>
                            </div>
                        </div>
                        
                        @endforeach
                        {{-- @dd($project) --}}
                        <div class="col-lg-3 col-md-4 col-sm-6 department-item">
                            <a class="nav-link card add-department-card" data-bs-toggle="modal" data-bs-target="#createDepartmentModal" data-project="{{ $project }}">
                                <div class="card-body text-center">
                                    <i class="fas fa-plus fa-3x mb-3"></i>
                                    <h5 class="card-title">{{ __('Créer un Département') }}</h5>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Modal for confirming department deletion -->
    <div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-light" id="deleteDepartmentModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4 class="text-danger">Tous les comptes dans ce service seront supprimés</h4>
                    <p>Êtes-vous sûr de vouloir supprimer ce département? <b> Cette action est irréversible. </b></p>
                </div>
                <div class="modal-footer">
                    <form id="deleteDepartmentForm" action="{{ route('annuaire.depart.delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="projectToDelete" name="project">
                        <input type="hidden" id="departmentToDelete" name="nomService">
                    
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>                                       
                </div>
            </div>
        </div>
    </div>
    {{-- Modal for creating new department --}}
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="createDepartmentModalLabel">{{ __('Crée un Nouveau Département') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="project_name" id="project-name">
                    
                    <div class="mb-3">
                        <label for="nomService" class="form-label">{{ __('Nom de Département') }}</label>
                        <input type="text" class="form-control" id="nomService" name="nomService" required>
                        <div class="invalid-feedback">
                            {{ __('Nom Département est requis.') }}
                        </div>
                    </div>
                    <div class="modal-footer py-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="nextButton">{{ __('Suivant') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for creating new user --}}
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="createEmployeeModalLabel">Ajouter un nouvel employé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createEmployeeForm" action="{{ route('annuaire.depart.store') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="service" id="user-service-name"> <!-- Hidden input for the service name -->

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
                                        <option value="ouvrier">Normal</option>
                                        <option value="responsable" selected>Responsable</option>
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
                                <input type="text" class="form-control" hidden id="create-emp-projet" name="projet" placeholder="Projet">
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
