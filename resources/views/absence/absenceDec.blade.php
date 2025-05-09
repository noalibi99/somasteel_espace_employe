@extends('layouts.app')

@push('vite')
    @vite(['resources/js/absenceDec.js'])
@endpush

@section('content')
<div class="container mx-auto py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            Déclaration d'absence
        </h2>
        <div id="clock" class="inline-flex items-center gap-2 bg-orange-50 rounded-full px-5 py-2 shadow">
    <span class="fa fa-calendar text-orange-500"></span>
    <span class="date text-gray-800 font-medium"></span>
    <span class="mx-2 text-orange-400">|</span>
    <span class="fa fa-clock text-orange-500"></span>
    <span class="time text-black font-bold"></span>
</div>
    </div>

    <div class="bg-white rounded-t-lg shadow">
    @if (Auth::user()->isRH())
    <!-- First row: unified search and date controls -->
    <div class="flex flex-wrap items-center justify-between p-4">
        <!-- Search on left -->
        <div class="relative w-full md:w-96 mb-3 md:mb-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fa-solid fa-magnifying-glass text-lg text-black"></i>
            </span>
            <input 
                id="searchInputRH"
                type="text" 
                placeholder="Rechercher" 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out"
                autocomplete="off"
            >
        </div>
        
        <!-- Settings dropdown and date form on right -->
        <div class="flex items-center space-x-3">
            <!-- Date form -->
            <form action="{{ route('absenceDec.index') }}" method="GET" class="flex items-center space-x-2 bg-white rounded-lg shadow px-3 py-2">
                @csrf
                <input type="date" name="date" id="date_to_export" value="{{ $today }}"
                       class="border border-gray-200 rounded-md px-2 py-1 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none transition" />
                <button class="bg-orange-500 text-white px-4 py-1.5 rounded-md hover:bg-orange-600 transition text-sm font-semibold shadow"
                        type="submit">
                    Chercher
                </button>
            </form>
            
            <!-- Settings dropdown -->
            <div class="relative">
                <button class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-full focus:outline-none shadow transition" id="settingsDropdownBtn">
                    <i class="fa fa-gear"></i>
                </button>
                <ul class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-20 hidden py-2" id="settingsDropdown">
                    <li>
                        <a class="flex items-center px-4 py-2 text-gray-900 hover:bg-orange-50 transition" id="export-button"
                           href="{{ route('export.shifts', ['date' => now()->toDateString() ]) }}">
                            <i class="fa fa-file mr-2 text-orange-500"></i> Export (.xlsx)
                        </a>
                    </li>
                    <li><hr class="my-1 border-t border-gray-100"></li>
                    <li>
                        <button class="flex items-center w-full text-left px-4 py-2 text-gray-900 hover:bg-orange-50 transition" id="openShiftModal">
                            <i class="fa fa-pencil mr-2 text-orange-500"></i> Shifts
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Second row: department tabs (full width) -->
    <div class="px-4 pb-3">
        <div class="inline-flex w-full rounded-lg bg-orange-500 p-1" id="shiftTabs" role="group">
            <button type="button"
                class="shift-btn flex-1 px-4 py-2 rounded-lg font-bold transition
                    bg-white text-orange-500 border border-orange-500 active"
                data-shift-id="acierie">
                ACIERIE
            </button>
            <button type="button"
                class="shift-btn flex-1 px-4 py-2 rounded-lg font-bold transition
                    bg-orange-500 text-white"
                data-shift-id="laminoire">
                LAMINOIRE
            </button>
            <button type="button"
                class="shift-btn flex-1 px-4 py-2 rounded-lg font-bold transition
                    bg-orange-500 text-white"
                data-shift-id="administration">
                ADMINISTRATION
            </button>
            <button type="button"
                class="shift-btn flex-1 px-4 py-2 rounded-lg font-bold transition
                    bg-orange-500 text-white"
                data-shift-id="chauffeur">
                CHAUFFEUR
            </button>
        </div>
    </div>
    @endif
</div>

        <div class="p-0">
            @if (Auth::user()->isResponsable())
                @include('absence.absDecRespComponent')
            @endif

            @if (Auth::user()->isRH())
                @include('absence.absDecRHComponent')
            @endif
        </div>
    </div>
</div>
<div id="toaster" class="fixed top-6 right-6 z-50 hidden">
    <div id="toaster-content" class="bg-orange-500 text-white px-4 py-2 rounded shadow-lg flex items-center space-x-2">
        <span id="toaster-message"></span>
        <button onclick="document.getElementById('toaster').classList.add('hidden')" class="ml-2 text-white hover:text-orange-200 focus:outline-none">&times;</button>
    </div>
</div>

@endsection
@if (session('success'))
<script>
    window.addEventListener('load', function () {
        window.showToast(@json(session('success')), 'success');
    });
</script>
@endif