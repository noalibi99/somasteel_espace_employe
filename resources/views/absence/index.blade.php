@extends('layouts.app')
@push('vite')
    @vite(['resources/js/absence.js',])
@endpush

@section('content')
<section class="container my-2">
    
    <div class="d-flex justify-content-between my-3 row">
        <h2 class="col">
            <b class="border-bottom border-black border-2 no-break"><em>{{__('Permissions d\'Absence')}} </em></b>
        </h2>
        <div class="col text-end">
            <button class="btn btn-warning no-break" id="demander-conger">
                <i class="fa fa-plus me-2"></i> {{__(' Demande de Permission')}}
            </button>
        </div>
    </div>

    <div class="overflow-x-scroll">
        <table id="permissions" class=" table table-striped" >
            <thead>
                <tr>
                    <th class="no-break">Nom</th>
                    <th class="no-break">Prénom</th>
                    <th class="no-break">date</th>
                    <th class="no-break">Status</th>
                    <!-- <th class="no-break">Solde congé (actuel)</th> -->
                    
                    <th class="no-break">Motif</th>
                    {{-- @if(!$currentUser->isOuvrier()) --}}
                        <th class="no-break">Actions</th>
                    {{-- @endif --}}
                    <th class="no-break">date creation</th>

                </tr>
            </thead>
            
        </table>
    </div>
</section>
@endsection