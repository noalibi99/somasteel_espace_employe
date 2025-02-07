@extends('layouts.app')

@push('vite')
    @vite(['resources/js/absenceDec.js'])
@endpush

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between my-3 row">
            <h2 class="col">
                <b class="border-bottom border-black border-2 no-break"><em>{{ __('Déclaration d\'absence') }} </em></b>
            </h2>
            <div class="col d-flex justify-content-end align-items-center">
                <div id="clock">
                    <p class="date no-break"></p>
                    <p class="time no-break"></p>
                </div>
            </div>
        </div>

        <div class="overflow-x-scroll">
            <div class="card text-center">
                <div class="card-header">
                    <div class="row d-flex justify-content-between">
                        @if (Auth::user()->isRH())
                            {{-- <div class="col">
                <select id="group-select" class="form-select float-start w-fc">
                    @foreach ($shifts->groupBy('group')->sortKeysDesc() as $group => $shiftGroup)
                        <option value="{{ $group }}" >{{ $group }}</option>
                    @endforeach
                </select>
            </div> --}}
                            <div class="col">
                                <div class="d-flex justify-content-start">
                                    <button type="button" class="btn btn-sm btn-warning mt-1" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa fa-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="btn dropdown-item float-start" id="export-button"
                                                href="{{ route('export.shifts', ['date' => request('date')]) }}">
                                                <i class="fa fa-file"></i> Export(.xlsx)
                                            </a>
                                        </li>
                                        <li>
                                            <a class="btn dropdown-item float-start" data-bs-toggle="modal" data-bs-target="#editShiftModal">
                                                <i class="fa fa-pencil"></i> shifts
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col d-flex align-items-center gap-2">
                                <div>
                                    <form action="{{ route('absenceDec.index') }}" method="GET" class="d-flex">
                                        @csrf
                                        <input type="date" name="date" value="{{ $today }}"
                                            class="form-control mt-1 me-1">
                                        <button class="btn btn-sm btn-outline-primary py-0" type="submit"> chercher
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <ul class="nav nav-tabs card-header-tabs w-fc float-end">
                                    <li class="nav-item">
                                        <a class="shift nav-link text-black fw-bolder " href="#" data-shift-id="acierie">ACIERIE</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="shift nav-link text-black fw-bolder active" href="#" data-shift-id="laminoire">LAMINOIRE</a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                @if (Auth::user()->isResponsable())
                    @include('absence.absDecRespComponent')
                @endif

                @if (Auth::user()->isRH())
                    @include('absence.absDecRHComponent')
                @endif
            </div>
        </div>
    </div>
    
    <script>
        function setAttendanceStatus(userId, status) {
            const statusInput = document.getElementById(`status_${userId}`);
            statusInput.value = status;

            // Toggle the active class for the buttons
            const presentBtn = document.querySelector(`#attendance-buttons-${userId} .présent`);
            const absentBtn = document.querySelector(`#attendance-buttons-${userId} .absent`);

            if (status === 'Présent') {
                presentBtn.classList.add('active');
                absentBtn.classList.remove('active');
            } else {
                presentBtn.classList.remove('active');
                absentBtn.classList.add('active');
            }
        }
    </script>
@endsection
