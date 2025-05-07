@extends('layouts.app')

@section('title', $employee->nom . ' ' . $employee->prénom)

@section('content')
<style>
</style>
<div class="container mt-4 p-sm-2 ">
    {{-- ignore --}}
    <div class="row d-flex justify-content-center">
        <div class="card col-lg-6 px-0">
            <div class="card-header">
                <h2>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fa fa-angle-left" aria-hidden="true"></i>
                    </a>
                    
                    <u>{{ $employee->nom }} {{ $employee->prénom }}</u>
                </h2>
            </div>
            <div class="card-body">
                <div class="row w-100 d-flex justify-content-center mb-3">
                    <img src="http://127.0.0.1:8000/images/somasteel.jpg" class="custum-file-upload mx-0 px-0">
                </div>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Nom Prénom:</strong></div>
                    <div class="col-8 p-0">{{ $employee->nom }} {{ $employee->prénom }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Email:</strong></div>
                    <div class="col-8 p-0">{{ $employee->email }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Matricule:</strong></div>
                    <div class="col-8 p-0">{{ $employee->matricule }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Fonction:</strong></div>
                    <div class="col-8 p-0">{{ $employee->fonction }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Service:</strong></div>
                    <div class="col-8 p-0">{{ $employee->service }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Type:</strong></div>
                    <div class="col-8 p-0">{{ $employee->type }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Solde Congé:</strong></div>
                    <div class="col-8 p-0">{{ $employee->solde_conge }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Responsable Hiérarchique:</strong></div>
                    <div class="col-8 p-0">{{ $employee->responsable_hiarchique }}</div>
                </h5>
                <h5 class="row mb-3 ps-2">
                    <div class="col-4 p-0"><strong>Directeur:</strong></div>
                    <div class="col-8 p-0">{{ $employee->directeur }}</div>
                </h5>
            </div>
            <div class="card-footer text-right">
                <div class="btn-group">
                    <a href="#" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i> Modifier </a>
                    <a href="#" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> supprimer </a>
                </div>
                <a href="#" class="btn btn-primary float-end"><i class="fa fa-lock" aria-hidden="true"></i> changer Mot de Pass </a>
            </div>
        </div>
    </div>
</div>

@endsection