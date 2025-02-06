@props(['tableId']) 

<thead>
    <tr>
        <th>Matricule</th>
        <th>Employés</th>
        <th>
            <div class="d-flex align-items-center gap-3">
                Service
                <select id="service-filter-{{ $tableId }}" class="form-select service-filter" data-table="{{ $tableId }}">
                    <option value="">All</option>
                    @foreach ($uniqueServices as $service)
                        <option value="{{ $service->service }}">{{ $service->service }}</option>
                    @endforeach
                </select>
            </div>
        </th>
        <th>
            <div class="d-flex align-items-center gap-3">
                Présence
                <select id="presence-filter-{{ $tableId }}" class="form-select presence-filter" data-table="{{ $tableId }}">
                    <option value="">All</option>
                    <option value="Présent">Présent</option>
                    <option value="Absent">Absent</option>
                    <option value="Non déclaré">Non déclaré</option>
                </select>
            </div>
        </th>
        <th>Shift</th>
    </tr>
</thead>
