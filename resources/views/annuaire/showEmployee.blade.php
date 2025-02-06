@extends('layouts.app')

@push('vite')
@vite(['resources/js/employee.js'])
@endpush
@section('content')
<style>

</style>
<div class="container mt-4 p-sm-2 ">
    <div class="row d-flex justify-content-center">
        <div class="card col-lg-6 px-0">
            <div class="card-header">
                <h2>
                    <a href="{{ route('annuaire.depart', [$employee->projet, $employee->service]) }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-angle-left" aria-hidden="true"></i>
                    </a>
                    <u>{{ $employee->nom }} {{ $employee->prénom }}</u>
                </h2>
            </div>
            <form action="{{ route('annuaire.employee.update', [$employee->projet, $employee->id]) }}" method="POST">
                @csrf
                @method('PUT')
            <div class="card-body pe-4">    
                <div class="row w-100 d-flex justify-content-center mb-3">
                    @if ($employee->profile_picture)
                            <img 
                            class="custom-file-upload mx-0 px-0"
                            src='{{ route('profile.image', basename($employee->profile_picture)) }}'
                            >
                        @else
                            <span class="custom-file-upload">
                                <i class="fa fa-user fa-5x" aria-hidden="true"></i>
                            </span>
                        @endif
                </div>
                <div class="field-container mb-3 ps-2" id="nom-prénom-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Nom Prénom:</strong></div>
                        <div class="col-8">
                            <span class="field-display">{{ $employee->nom }} {{ $employee->prénom }}</span>
                            <div class="row d-flex align-content-center gap-1 py-0 my-0 ">
                                <input type="text" name="nom" value="{{ $employee->nom }}"
                                    class="field-edit form-control col d-none ">
                                <input type="text" name="prénom" value="{{ $employee->prénom }}"
                                    class="field-edit form-control col d-none ">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="email-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Email:</strong></div>
                        <div class="col-8 p-0 my-0">
                            <span class="field-display">{{ $employee->email }}</span>
                            <input type="email" name="email" value="{{ $employee->email }}"
                                class="field-edit form-control d-none">
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="matricule-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Matricule:</strong></div>
                        <div class="col-8 p-0 my-0 py-0">
                            <span class="field-display">{{ $employee->matricule }}</span>
                            <input type="text" name="matricule" value="{{ $employee->matricule }}"
                                class="field-edit form-control d-none">
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="fonction-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Fonction:</strong></div>
                        <div class="col-8 p-0 my-0 py-0">
                            <span class="field-display">{{ $employee->fonction }}</span>
                            <input type="text" name="fonction" value="{{ $employee->fonction }}"
                                class="field-edit form-control d-none">
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="service-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Service:</strong></div>
                        <div class="col-8 p-0 my-0 py-0">
                            <span class="field-display">{{ $employee->service }}</span>
                            <select class="form-select form-select-md field-edit d-none" id="service" name="service" value="{{ old('type') }}" required>
                                @foreach ($services as $service)
                                    <option @if ($employee->service == $service) @selected(true) @endif value="{{$service}}" >{{ $service }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="type-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Type:</strong></div>
                        <div class="col-8 p-0 my-0 py-0">
                            {{-- type cases --}}
                            <span class="field-display">
                                @if ($employee->type =='rh') {{ 'Ressources Humaines' }}
                                @elseif($employee->type =='administrateur') {{ 'Admin' }}
                                @elseif($employee->type =='directeur') {{ 'Directeur' }}
                                @elseif ($employee->type =='responsable') {{ 'Responsable' }}
                                @else {{ 'Normal' }}
                                @endif
                            </span>
                            <select class="form-select form-select-md field-edit d-none" id="type" name="type" value="{{ old('type') }}" required>
                                <option value="ouvrier" @if ($employee->type =='ouvrier') @selected(true) @endif >Normal</option>
                                <option value="responsable" @if ($employee->type =='responsable') @selected(true) @endif>Responsable</option>
                                <option value="directeur" @if ($employee->type =='directeur') @selected(true) @endif>Directeur</option>
                                <option value="rh" @if ($employee->type =='rh') @selected(true) @endif>Ressources Humaines</option>
                                <option value="administrateur" @if ($employee->type =='administrateur') @selected(true) @endif>Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="solde-conge-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Solde Congé:</strong></div>
                        <div class="col-8 p-0 my-0 py-0">
                            <span class="field-display">{{ $employee->solde_conge }}</span>
                            <input type="number" name="solde_conge" value="{{ $employee->solde_conge }}"
                                class="field-edit form-control d-none">
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="responsable-hierarchique-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Responsable Hiérarchique:</strong></div>
                        <div class="col-8 p-0 my-0 py-0 d-flex gap-1">
                            <span class="field-display">{{ $employee->responsable_hiarchique }}</span>
                            <input type="text" id="responsable_hiarchique" class="field-edit form-control d-none" value="{{ $employee->responsable_hiarchique ?? '' }}">
                            <input type="text" name="responsable_hiarchique" id="responsable_hiarchique_matricule"
                                value="{{ $employee->resp_matricule ?? null }}" @readonly(true) class="field-edit w-25 text-center bg-secondary-subtle form-control d-none">
                        </div>
                    </div>
                </div>
                <div class="field-container mb-3 ps-2" id="directeur-container">
                    <div class="row">
                        <div class="col-4 p-0"><strong>Directeur:</strong></div>
                        <div class="col-8 p-0 my-0 py-0 d-flex gap-1">
                            <span class="field-display">{{ $employee->directeur }}</span>
                            <input type="text" class="field-edit form-control d-none" id="directeur" value="{{ $employee->directeur ?? '' }}">
                            <input type="text" name="directeur" id="directeur_matricule" value="{{ $employee->dir_matricule ?? null }}" @readonly(true)
                                class="field-edit w-25 text-center bg-secondary-subtle form-control d-none">
                        </div>
                    </div>
                </div>
            </div>
            <script>
                var responsables = {!! json_encode($responsables) !!};
                var directeurs = {!! json_encode($directeurs) !!};
            </script>
            <div class="card-footer text-start align-items-center">
                <div class="btn-group">
                    <button type="submit" id="save-button" class="btn btn-sm btn-warning d-none"><i class="fa fa-check"
                            aria-hidden="true"></i> Enregistrer</button>
                    <button type="button" id="cancel-button" class="btn btn-sm btn-secondary d-none"><i
                            class="fa fa-times" aria-hidden="true"></i> Annuler</button>
                    <button type="button" id="edit-button" class="btn btn-sm btn-success rounded-start-1 "><i
                            class="fa fa-pencil" aria-hidden="true"></i> Modifier</button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-employee-id="{{ $employee->id }}" data-employee-name="{{ $employee->nom }} {{ $employee->prénom }}">
                        <i class="fa fa-trash" aria-hidden="true"></i> Supprimer
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fa fa-lock" aria-hidden="true"></i> Changer Mot de Passe
                </button>
            </div>
            </form>
        </div>
        {{-- DELETE CONFIRMATION MODEL --}}
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
        {{-- CHANGE PASS --}}
        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="changePasswordModalLabel">Changer le mot de passe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="changePasswordForm" action="{{ route('annuaire.employee.changePassword', $employee->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3 input-group">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="newPassword" name="new_password" value="{{ old('new_password') }}" placeholder="Nouveau mot de passe" required minlength="8">
                                    <label for="newPassword">Nouveau mot de passe</label>
                                    <div class="invalid-feedback">Le mot de passe est requis et doit contenir au moins 8 caractères.</div>
                                </div>
                                <button class="input-group-text eye-icon" type="button" id="togglePasswordVisibility">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="mb-3 form-floating">
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_new_password" value="{{ old('confirm_new_password') }}" placeholder="Confirmation du mot de passe" required minlength="8">
                                <label for="confirmPassword">Confirmation du mot de passe</label>
                                <div class="invalid-feedback">La confirmation du mot de passe est incorrecte.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Changer le mot de passe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



    </div>
</div>

@endsection