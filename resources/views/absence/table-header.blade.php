@props(['tableId'])

<thead class="bg-orange-50">
    <tr>
        <th class="px-4 py-2 text-center">Matricule</th>
        <th class="px-4 py-2 text-center">Employés</th>
        <th class="px-4 py-2 text-center">
            <div class="flex items-center justify-center space-x-2">
                <span>Service</span>
                <select id="service-filter-{{ $tableId }}" class="service-filter border rounded px-2 py-1 text-sm" data-table="{{ $tableId }}">
                    <option value="">All</option>
                    @foreach ($uniqueServices as $service)
                        <option value="{{ $service->service }}">{{ $service->service }}</option>
                    @endforeach
                </select>
            </div>
        </th>
        <th class="px-4 py-2 text-center">
            <div class="flex items-center justify-center space-x-2">
                <span>Présence</span>
                <select id="presence-filter-{{ $tableId }}" class="presence-filter border rounded px-2 py-1 text-sm" data-table="{{ $tableId }}">
                    <option value="">All</option>
                    <option value="Présent">Présent</option>
                    <option value="Absent">Absent</option>
                    <option value="Non déclaré">Non déclaré</option>
                </select>
            </div>
        </th>
        <th class="px-4 py-2 text-center">Shift</th>
    </tr>
</thead>