<div class="card-body p-0 pb-2 shift-card-body">
    <form action="{{ route('attendance.declare') }}" method="POST">
        @csrf
        <table class="table table-bordered shift-table">
            <thead>
                <tr>
                    <th>Nom & Prénom</th>
                    <th>Présence</th>
                    <th>Shift</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usersLA as $user)
                    @php
                        $status = '';
                        $selectedShift = null; // Initialize selectedShift variable

                        // Iterate through declaredAttendances to find the status and shift for the current user
                        foreach ($declaredAttendances as $attendance) {
                            if ($attendance->user_id == $user->id) {
                                $status = $attendance->status;
                                $selectedShift = $attendance->shift_id; // Set the selected shift for the user
                                break;
                            }
                        }
                    @endphp
                    <tr id="user-row-{{ $user->id }}">
                        <td>{{ $user->nom . ' ' . $user->prénom }}</td>
                        <td class="d-flex justify-content-center">
                            <div class="attendance-buttons" id="attendance-buttons-{{ $user->id }}">
                                <div class="btn-group" role="group">
                                    {{-- Attendance buttons for Présent and Absent --}}
                                    <button type="button"
                                        class="btn btn-secondary btn-status présent
        @if ($status === 'Présent') active @endif"
                                        data-user-id="{{ $user->id }}"
                                        data-status="Présent">P</button>

                                    <button type="button"
                                        class="btn btn-secondary btn-status absent
        @if ($status === 'Absent') active @endif"
                                        data-user-id="{{ $user->id }}"
                                        data-status="Absent">A</button>
                                </div>
                                <input type="hidden" name="status[{{ $user->id }}]"
                                    id="status-{{ $user->id }}" value="{{ $status }}">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <select class="form-select shift-select"
                                    name="shift[{{ $user->id }}]" id="shift-{{ $user->id }}">
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                            @if ($shift->id == $selectedShift) selected @endif>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-warning mt-2">Submit Attendance</button>
    </form>
</div>