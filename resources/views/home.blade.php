@extends('layouts.app')

@push('vite')
    @vite(['resources/js/profile.js',])
@endpush
@section('content')
<section class="container ">
    {{-- @dd($userInfo) --}}
    <div class="row px-2 gap-2 d-flex justify-content-center ">
        <!-- <div class="col-sm-12 col-lg-6"> -->
        <div class="card px-0 my-4 col-12 col-lg-8 ">
            <div class="card-header position-relative">
                <h4 class="d-flex justify-content-center">
                    <b><em>Mes Informations</em></b>
                </h4>
                <div id="modifyMenu" class="dropdown">
                    <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>@if ($userInfo->profile_picture)
                            <form action="{{ route('home.delete.picture') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button id="deletePicButton" type="submit" class="dropdown-item"><i class="fa fa-trash"></i> Supprimé Photo Profile</button>
                            </form>
                            @endif</li>
                        <li>
                            <button id="edit-button" class="dropdown-item"><i class="fa fa-pencil me-1" aria-hidden="true"></i>Modifier Email</button>
                        </li>
                        <hr class="my-1">
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fa fa-lock" aria-hidden="true"></i> Changer Mot de Passe
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <form id="profilePictureForm" action="{{ route('profile.updatePicture') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                <div class="row justify-content-center">
                    <label class="custom-file-upload {{ $userInfo->profile_picture ? '' : 'fa fa-user' }} fa-6x user-img col-3 mx-0 px-0 col-lg-2 col-md-2 d-flex justify-content-center"
                        for="file" style="background-image: url('{{ $userInfo->profile_picture ? route('profile.image', basename($userInfo->profile_picture)) : '' }}');">
                            <input type="file" id="file" name="profile_picture" hidden />
                            @if (!$userInfo->profile_picture)
                                <small style="font-size: 0.6rem; padding-top: 5px;">Clicker ici</small>
                            @endif
                        </label>
                        <div class="d-flex justify-content-center my-2">
                            <h4 class="no-break border-bottom" id="full-name">
                                <strong>{{$userInfo->nom . " " . $userInfo->prénom}} </strong>
                            </h4>
                        </div>
                    </div>
                </form>
                

                <div class="col d-flex align-items-center">
                    <div class="row p-0 m-0 w-100 ">
                        <div class="col">
                            <div class="mx-1">
                                <h5 class="no-break" id="matricul">
                                    <strong>Matricul: </strong> {{$userInfo->matricule}}
                                </h5>
                            </div>
                            <div class="mx-1">
                                <h5 class="no-break" id="fonction">
                                    <strong>Fonction: </strong> {{$userInfo->fonction}}
                                </h5>
                            </div>
                            <div class="mx-1">
                                <h5 class="no-break" id="service">
                                    <strong>Service: </strong>{{$userInfo->service}}
                                </h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mx-1">
                                @if ($userInfo->email)
                                    <h5 class="" id="emailSection">
                                        <strong>Email: </strong> <span id="email-display">{{$userInfo->email}}</span>
                                    </h5>
                                    <div id="email-form-container" class="mb-1 d-none">
                                        <form action="{{ route('home.update') }}" method="POST" class="d-flex w-100">
                                            @csrf
                                            @method('PUT')
                                            <input type="email" value="{{ old('email') }}" name="email" class="form-control w-75" placeholder="Email (example@email.com)">
                                            <button type="submit" class="btn btn-warning ms-1 py-1"><i class="fa fa-check" aria-hidden="true"></i></button>
                                            <button type="button" id="cancel-button" class="btn btn-secondary ms-1 py-1"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        </form>
                                    </div>

                                @else
                                    <div class="alert alert-warn alert-warning alert-dismissible fade show " role="alert">
                                        <p>
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Vous devez entrer votre <b>adresse e-mail</b> pour recevoir les notifications de vos demandes.
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </p>
                                    </div>
                                    <div class="mb-1">
                                        <form action="{{ route('home.update') }}" method="POST"  class="d-flex">
                                            @csrf
                                            @method('PUT')
                                            <input type="email" value="{{ old('email') }}" name="email" class="form-control w-75" placeholder="Email (example@email.com)">
                                            <button type="submit" class="btn btn-warning ms-1 py-1"><i class="fa fa-check" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            <div class="mx-1">
                                <h5 class="" id="service">
                                    <strong>Responsable Hiérarchique: </strong> <span class="no-break">{{ ($userResp->nom ?? '') . ' ' . ($userResp->prénom ?? '')}} </span>
                                </h5>
                            </div>
                            <div class="mx-1">
                                <h5 class="" id="service">
                                    <strong>Solde de Congé: </strong> <span class="no-break"> {{$userInfo->solde_conge ?? "N/A"}} Jours</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                <div class="row w-100">
                    <div class="col">
                        <h6><strong>Dernier Demande Congé: </strong> 
                        <span class="d-inline-block pop-refus" tabindex="0" data-bs-toggle="popover"
                            data-bs-placement="top" data-bs-trigger="hover focus"
                            data-bs-content="{{$dcinfo->status }}">
                            @if($dcinfo->status === 'Refusé')
                            <div class="border rounded-1 p-1">
                                <i class="fa fa-circle-xmark status-red "></i> <span>{{  ($dcinfo->raison_refus ? ("Raison: " . $dcinfo->raison_refus) : '')}}</span>
                            </div>
                            
                            @elseif ($dcinfo->v_rh || $dcinfo->status == 'Valider')
                            <a href="{{ route('demandeConge.downloadConge', $dcinfo->id ?? 0) }}" class="btn btn-primary py-1"  >
                                <i class="fa fa-file-pdf"></i>                            
                                {{ $file }}
                                <i class="fa fa-download"></i>
                            </a>
                            @elseif ($dcinfo->v_dir)
                            <i class="fa fa-circle status-orange"></i>
                            @elseif ($dcinfo->v_resp)
                            <i class="fa fa-circle status-yellow"></i>
                            @elseif($dcinfo->status == 'En Attend')
                            <i class="fa fa-circle status-gray"></i>
                            @else
                            {{ $file }}
                            @endif
                        </span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="changePasswordModalLabel">Changer le mot de passe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="changePasswordForm" action="{{ route('annuaire.employee.changePassword', auth()->user()->id) }}" method="POST" class="needs-validation" novalidate>
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
        {{-- @dd($dcinfo); --}}
        {{-- <div class="card px-0 my-4  col-lg-4 w-fc col-12">
            <div class="card-header">
                <h4 class="d-flex justify-content-center">
                    <b><em>Mes Notifications</em></b>
                </h4>
            </div>
            <div class="card-body p-1">
                <a href="{{ route('demandeConge.downloadConge', $dcinfo->id ?? 0) }}" class="btn btn-info py-1"  >
                    <span class="d-inline-block pop-refus" tabindex="0" data-bs-toggle="popover"
                            data-bs-placement="top" data-bs-trigger="hover focus"
                            data-bs-content="{{$dcinfo->status ?? ''}}">
                            @if($dcinfo->status === 'Refusé')
                            
                            <i class="fa fa-circle-xmark status-red "></i>
                            @elseif ($dcinfo->v_rh || $dcinfo->status == 'Valider')
                            <i class="fa fa-file-pdf"></i>                            
                            {{ $file }}
        
                            @elseif ($dcinfo->v_dir)
                            <i class="fa fa-circle status-orange"></i>
                            @elseif ($dcinfo->v_resp)
                            <i class="fa fa-circle status-yellow"></i>
                            @elseif($dcinfo->status == 'En Attend')
                            <i class="fa fa-circle status-gray"></i>
                            @else
                            {{ $file }}
                            @endif
                        </span>
                    <i class="fa fa-download"></i>
                </a>
                <table class="documents">
                    <tr class="border-bottom">
                        <th class="px-1">Type</th>
                        <th>Nom Fichier</th>
                        <th>Etats</th>
                        <th></th>
                    </tr>
                    <tr class="">
                        <td class="">
                            <i class="fa fa-file-pdf text-danger"></i>
                        </td>
                        <td>
                            <span >{{$file != 'NOT_FOUND' ? $file : __('il y a aucun fichier')}}</span>
                        </td>
                        <td class="status">
                        <span class="d-inline-block pop-refus" tabindex="0" data-bs-toggle="popover"
                            data-bs-placement="top" data-bs-trigger="hover focus"
                            data-bs-content="{{$dcinfo->status}}">
                            @if($dcinfo->status === 'Refusé')
                            <i class="fa fa-circle-xmark status-red "></i>
                            @elseif ($dcinfo->v_rh || $dcinfo->status == 'Valider')
                            <i class="fa fa-circle-check status-green"></i>
                            @elseif ($dcinfo->v_dir)
                            <i class="fa fa-circle status-orange"></i>
                            @elseif ($dcinfo->v_resp)
                            <i class="fa fa-circle status-yellow"></i>
                            @else
                            <i class="fa fa-circle status-"></i>
                            @endif
                        </span>
                        </td>
                        
                        <td>
                            <a href="{{ route('demandeConge.downloadConge', $dcinfo->id) }}" class="btn btn-info py-1">
                                <i class="fa fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    <tr class="">
                        <td>
                            <i class="fa fa-circle status-red"></i>
                        </td>
                        <td>
                            <span class="text-wrap">Bultin de Pay.pdf</span>
                        </td>
                        <td>
                            <i class="fa fa-circle status-orange"></i>
                        </td>
                        <td>
                            <span><button class="btn btn-warning py-1 download-disable">
                                    <i class="fa fa-download"></i>
                                </button></span>
                        </td>
                    </tr>
                    <tr class="">
                        <td class="">
                            <i class="fa fa-file-pdf text-danger"></i>
                        </td>
                        <td>
                            <span class="text-wrap">Attestation Travaille.pdf</span>
                        </td>
                        <td>
                            <i class="fa fa-circle-check status-green"></i>
                        </td>
                        <td>
                            <span>
                                <button class="btn btn-warning py-1">
                                    <i class="fa fa-download"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div> --}}

        {{-- notification part --}}

    </div>
</section>


{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>



<div class="card-body">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    {{ __('You are logged in!') }}
</div>
</div>
</div>
</div>
</div> --}}
@endsection