<div class="card-body p-0 pb-2 shift-card-body" id="laminoire">
    <!-- Result Statistics Table -->
    <table class="table shift-table" id="shift-table-laminoire">
        @include('absence.table-header', ['tableId' => 'laminoire'])

        <tbody>
            @foreach ($usersLaminoire as $user)
                @php
                    $status = 'Non déclaré'; // Default status if no attendance is found
                    $userShift = 'Non déclaré'; // Default shift if no shift is declared

                    // Loop through declared attendances to find status and shift_id
                    foreach ($declaredAttendances as $attendance) {
                        if ($attendance->user_id == $user->id) {
                            $status = $attendance->status;

                            // Find the shift name by matching shift_id with the shifts array
                            foreach ($shifts as $shift) {
                                if ($shift->id == $attendance->shift_id) {
                                    $userShift = $shift->name; // Get the shift name
                                    break;
                                }
                            }
                            break; // Break loop once the attendance for the user is found
                        }
                    }
                @endphp
                <tr>
                    <td class="align-middle">{{ $user->matricule }}</td>
                    <td class="align-middle">{{ $user->nom }} {{ $user->prénom }}</td>
                    <td class="align-middle">{{ $user->service }}</td>
                    <td class="align-middle">
                        <span
                            class="p-1 rounded {{ $status == 'Présent' ? 'text-bg-success' : ($status == 'Absent' ? 'text-bg-danger' : 'text-bg-warning') }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td class="align-middle">{{ $userShift }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card-body p-0 pb-2 shift-card-body d-none" id="acierie">
    <!-- Result Statistics Table -->
    <table class="table shift-table" id="shift-table-acierie">
        @include('absence.table-header', ['tableId' => 'acierie'])
        <tbody>
            @foreach ($usersAcierie as $user)
                @php
                    $status = 'Non déclaré'; // Default status if no attendance is found
                    $userShift = 'Non déclaré'; // Default shift if no shift is declared

                    // Loop through declared attendances to find status and shift_id
                    foreach ($declaredAttendances as $attendance) {
                        if ($attendance->user_id == $user->id) {
                            $status = $attendance->status;

                            // Find the shift name by matching shift_id with the shifts array
                            foreach ($shifts as $shift) {
                                if ($shift->id == $attendance->shift_id) {
                                    $userShift = $shift->name; // Get the shift name
                                    break;
                                }
                            }
                            break; // Break loop once the attendance for the user is found
                        }
                    }
                @endphp
                <tr>
                    <td class="align-middle">{{ $user->matricule }}</td>
                    <td class="align-middle">{{ $user->nom }} {{ $user->prénom }}</td>
                    <td class="align-middle">{{ $user->service }}</td>
                    <td class="align-middle">
                        <span
                            class="p-1 rounded {{ $status == 'Présent' ? 'text-bg-success' : ($status == 'Absent' ? 'text-bg-danger' : 'text-bg-warning') }}">
                            {{ $status }}
                        </span>
                    </td>
                    <td class="align-middle">{{ $userShift }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editShiftModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editShiftModalLabel" aria-hidden="true">
    <meta name="shifts-route" content="{{ route('shifts.index') }}">
    <div class="modal-dialog">
        <form id="shiftForm">
            @csrf
            <input type="hidden" id="shift_id">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h1 class="modal-title fs-5">Gestion des Horaires (Shifts)</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Table of Shifts -->
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="shiftTableBody"></tbody>
                    </table>

                    <!-- Form for Add/Edit Shift -->
                    
                    <div class="mb-3 form-floating">
                        <input type="text" id="name" class="form-control" placeholder="" disabled>
                        <label for="name">Nom</label>
                    </div>
                    
                    <div class="mb-3 form-floating">
                        <input type="time" id="start_time" class="form-control" placeholder="" required>
                        <label for="start_time">Heure de début</label>
                    </div>
                    
                    <div class="mb-3 form-floating">
                        <input type="time" id="end_time" class="form-control" placeholder="" required>
                        <label for="end_time">Heure de fin</label>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveShift">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
