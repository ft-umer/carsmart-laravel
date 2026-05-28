<table class="w-full text-sm">
    <thead>
        <tr>
            <th></th>
            <th>Listing</th>
            <th>Vehicle</th>
            <th>VRM</th>
            <th>Mileage</th>
            <th>Valuation</th>
            <th>Δ</th>
            <th>QA</th>
            <th>State</th>
            <th>BIN</th>
            <th>Owner</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($listings as $l)
        <tr>
            <td><input type="checkbox" /></td>

            <td>{{ $l['ref'] }}</td>
            <td>{{ $l['vehicle'] }}</td>
            <td>{{ $l['vrm'] }}</td>
            <td>{{ number_format($l['mileage']) }}</td>

            <td>£{{ number_format($l['valuation']) }}</td>

            <td class="text-green-600">
                +£200 <!-- Phase 1 required delta -->
            </td>

            <td>{{ $l['qa'] }}</td>

            <td>
                <span class="badge">{{ $l['state'] }}</span>
            </td>

            <td>
                {{ $l['bin'] ? 'BIN ON' : 'OFF' }}
            </td>

            <td>{{ $l['owner'] }}</td>

            <td class="flex gap-2">
                <button class="btn btn-primary open-detail">Open</button>
                <button class="btn quick-view">Quick</button>
                <button class="btn">Pull Valuation</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>