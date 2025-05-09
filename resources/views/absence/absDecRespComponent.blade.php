<div class="shift-card-body bg-white rounded-lg shadow" id="responsable-shift-table">
    <form action="{{ route('attendance.declare') }}" method="POST">
        @csrf
        <div class="relative w-60 m-4">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fa-solid fa-magnifying-glass text-lg text-black"></i>
            </span>
            <input 
                id="searchInput"
                type="text" 
                placeholder="Rechercher " 
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-somasteel-orange/90 focus:border-somasteel-orange transition duration-150 ease-in-out">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded shadow shift-table" id="shift-table-responsable">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="px-4 py-2 text-center text-black font-bold border-b border-orange-200">Matricule</th>
                        <th class="px-4 py-2 text-center text-black font-bold border-b border-orange-200">Nom & Prénom</th>
                        <th class="px-4 py-2 text-center text-black font-bold border-b border-orange-200">Service</th>
                        <th class="px-4 py-2 text-center text-black font-bold border-b border-orange-200">Présence</th>
                        <th class="px-4 py-2 text-center text-black font-bold border-b border-orange-200">Shift</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usersLA as $user)
                        @php
                            $status = '';
                            $selectedShift = null;
                            foreach ($declaredAttendances as $attendance) {
                                if ($attendance->user_id == $user->id) {
                                    $status = $attendance->status;
                                    $selectedShift = $attendance->shift_id;
                                    break;
                                }
                            }
                        @endphp
                        <tr>
                            <td class="px-4 py-2 text-center">{{ $user->matricule }}</td>
                            <td class="px-4 py-2 text-center">{{ $user->nom }} {{ $user->prénom }}</td>
                            <td class="px-4 py-2 text-center">{{ $user->service }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="inline-flex rounded-lg bg-orange-100 p-1" id="attendance-buttons-{{ $user->id }}">
                                    <button type="button"
                                        class="btn-status px-3 py-1 rounded-l-lg font-bold transition
                                            {{ $status === 'Présent' ? 'bg-green-500 text-white' : 'bg-gray-200 text-black' }}"
                                        data-user-id="{{ $user->id }}"
                                        data-status="Présent">
                                        P
                                    </button>
                                    <button type="button"
                                        class="btn-status px-3 py-1 rounded-r-lg font-bold transition
                                            {{ $status === 'Absent' ? 'bg-red-500 text-white' : 'bg-gray-200 text-black' }}"
                                        data-user-id="{{ $user->id }}"
                                        data-status="Absent">
                                        A
                                    </button>
                                    <input type="hidden" name="status[{{ $user->id }}]"
                                        id="status-{{ $user->id }}" value="{{ $status }}">
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <select class="form-select shift-select border border-gray-300 rounded px-8 py-2 focus:ring-2 focus:ring-orange-400"
                                    name="shift[{{ $user->id }}]" id="shift-{{ $user->id }}">
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                            @if ($shift->id == $selectedShift) selected @endif>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-center">
            <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-md hover:bg-orange-600 transition font-semibold shadow mb-6">
                Soumettre la présence
            </button>
        </div>
    </form>
</div>

