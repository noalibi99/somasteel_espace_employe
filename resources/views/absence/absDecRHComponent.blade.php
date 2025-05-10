<div class="shift-card-body {{ $activeTab !== 'laminoire' ? 'hidden' : '' }}" id="laminoire">

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow shift-table"  id="shift-table-laminoire">
            @include('absence.table-header', ['tableId' => 'laminoire'])
            <tbody>
                @foreach ($usersLaminoire as $user)
                    @php
                        $status = 'Non déclaré';
                        $userShift = 'Non déclaré';
                        foreach ($declaredAttendances as $attendance) {
                            if ($attendance->user_id == $user->id) {
                                $status = $attendance->status;
                                foreach ($shifts as $shift) {
                                    if ($shift->id == $attendance->shift_id) {
                                        $userShift = $shift->name;
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-center">{{ $user->matricule }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->nom }} {{ $user->prénom }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->service }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $status == 'Présent' ? 'bg-green-100 text-green-700' : ($status == 'Absent' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">{{ $userShift }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $usersLaminoire->appends(['tab' => 'laminoire'])->links() }}
</div>

<div class="shift-card-body {{ $activeTab !== 'acierie' ? 'hidden' : '' }}" id="acierie">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow shift-table" id="shift-table-acierie">
            @include('absence.table-header', ['tableId' => 'acierie'])
            <tbody>
                @foreach ($usersAcierie as $user)
                    @php
                        $status = 'Non déclaré';
                        $userShift = 'Non déclaré';
                        foreach ($declaredAttendances as $attendance) {
                            if ($attendance->user_id == $user->id) {
                                $status = $attendance->status;
                                foreach ($shifts as $shift) {
                                    if ($shift->id == $attendance->shift_id) {
                                        $userShift = $shift->name;
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-center">{{ $user->matricule }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->nom }} {{ $user->prénom }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->service }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $status == 'Présent' ? 'bg-green-100 text-green-700' : ($status == 'Absent' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">{{ $userShift }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $usersAcierie->appends(['tab' => 'acierie'])->links() }}
</div>

<div class="shift-card-body {{ $activeTab !== 'administration' ? 'hidden' : '' }}" id="administration">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow shift-table" id="shift-table-administration">
            @include('absence.table-header', ['tableId' => 'administration'])
            <tbody>
                @foreach ($usersAdministration as $user)
                    @php
                        $status = 'Non déclaré';
                        $userShift = 'Non déclaré';
                        foreach ($declaredAttendances as $attendance) {
                            if ($attendance->user_id == $user->id) {
                                $status = $attendance->status;
                                foreach ($shifts as $shift) {
                                    if ($shift->id == $attendance->shift_id) {
                                        $userShift = $shift->name;
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-center">{{ $user->matricule }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->nom }} {{ $user->prénom }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->service }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $status == 'Présent' ? 'bg-green-100 text-green-700' : ($status == 'Absent' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">{{ $userShift }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    {{ $usersAdministration->appends(['tab' => 'administration'])->links() }}
</div>

<div class="shift-card-body {{ $activeTab !== 'chauffeur' ? 'hidden' : '' }}" id="chauffeur">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow shift-table" id="shift-table-chaffeur">
            @include('absence.table-header', ['tableId' => 'chauffeur'])
            <tbody>
                @foreach ($usersChauffeur as $user)
                    @php
                        $status = 'Non déclaré';
                        $userShift = 'Non déclaré';
                        foreach ($declaredAttendances as $attendance) {
                            if ($attendance->user_id == $user->id) {
                                $status = $attendance->status;
                                foreach ($shifts as $shift) {
                                    if ($shift->id == $attendance->shift_id) {
                                        $userShift = $shift->name;
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-center">{{ $user->matricule }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->nom }} {{ $user->prénom }}</td>
                        <td class="px-4 py-2 text-center">{{ $user->service }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $status == 'Présent' ? 'bg-green-100 text-green-700' : ($status == 'Absent' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center">{{ $userShift }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $usersChauffeur->appends(['tab' => 'chauffeur'])->links() }}
</div>


<!-- Shift Modal -->
<div id="editShiftModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
        <div class="flex justify-between items-center px-4 py-2 bg-orange-500 rounded-t-lg">
            <h2 class="text-white text-lg font-bold">Gestion des Horaires (Shifts)</h2>
            <button id="closeShiftModal" class="text-white text-2xl leading-none">&times;</button>
        </div>
        <div class="p-4 overflow-x-auto">
        <table class="min-w-full text-sm mb-4 border border-gray-200 rounded-lg overflow-hidden shadow">
    <thead class="bg-orange-50">
        <tr>
            <th class="py-3 px-4 text-orange-600 font-bold text-left border-b border-orange-200">Name</th>
            <th class="py-3 px-4 text-orange-600 font-bold text-left border-b border-orange-200">Start</th>
            <th class="py-3 px-4 text-orange-600 font-bold text-left border-b border-orange-200">End</th>
            <th class="py-3 px-4 text-orange-600 font-bold text-left border-b border-orange-200">Actions</th>
        </tr>
    </thead>
    <!--
    hover:bg-orange-100
    hover:text-orange-800
    hover:shadow
-->
    <tbody id="shiftTableBody" class="bg-white"></tbody>
</table>
<form id="shiftForm">
            <input type="hidden" id="shift_id" name="shift_id">
            <div class="mb-3">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" id="name" class="form-input w-full border-gray-300 rounded" disabled>
            </div>
            <div class="mb-3">
                <label for="start_time" class="block text-sm font-medium text-gray-700">Heure de début</label>
                <input type="time" id="start_time" class="form-input w-full border-gray-300 rounded" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="block text-sm font-medium text-gray-700">Heure de fin</label>
                <input type="time" id="end_time" class="form-input w-full border-gray-300 rounded" required>
            </div>
        </div>
        <div class="flex justify-end gap-2 px-4 py-2 border-t">
            <button id="closeShiftModalFooter" class="bg-gray-200 text-gray-700 rounded px-4 py-1">Close</button>
            <button type="button" id="saveShift" class="bg-orange-500 text-white rounded px-4 py-1">Save</button>
        </div>
                    </form>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
    <h2 class="text-lg font-bold text-red-600 mb-2">Confirmer la suppression</h2>
    <p class="mb-4 text-gray-700">Êtes-vous sûr de vouloir supprimer ce shift ?</p>
    <div class="flex justify-end space-x-2">
      <button id="cancelDeleteBtn" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700">Annuler</button>
      <button id="confirmDeleteBtn" class="px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white">Supprimer</button>
    </div>
  </div>
</div>
