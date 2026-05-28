<table class="table">
<thead>
<tr>
<th>Ref</th><th>Vehicle</th><th>VRM</th><th>Mileage</th><th>Valuation</th><th>QA</th><th>State</th><th>Owner</th><th></th>
</tr>
</thead>

<tbody>
@foreach($listings as $l)
<tr>
<td>{{ $l['ref'] }}</td>
<td>{{ $l['vehicle'] }}</td>
<td>{{ $l['vrm'] }}</td>
<td>{{ $l['mileage'] }}</td>
<td>£{{ $l['valuation'] }}</td>
<td>{{ $l['qa'] }}</td>
<td>{{ $l['state'] }}</td>
<td>{{ $l['owner'] }}</td>
<td>
<a class="btn btn-sm btn-primary" href="/listings/{{ $l['id'] }}">Open</a>
</td>
</tr>
@endforeach
</tbody>
</table>