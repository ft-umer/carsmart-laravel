<div class="card p-3">
<h5>Valuations</h5>

@foreach($valuations as $v)
<div class="border-bottom py-2">
<strong>{{ $v['source'] }}</strong><br>
£{{ $v['amount'] }} <small>{{ $v['time'] }}</small>
</div>
@endforeach

<button class="btn btn-primary w-100 mt-2">
Pull Latest Valuation
</button>

</div>