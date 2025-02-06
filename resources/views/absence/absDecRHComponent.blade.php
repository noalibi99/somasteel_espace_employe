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