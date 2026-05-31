<div class="overflow-x-auto">

    <table class="w-full text-sm">
        <thead>
            <tr>
                <th></th>
                <th>Listing</th>
                <th>Vehicle</th>
                <th>VRM</th>
                <th>Mileage</th>
                <th>Valuation</th>
                <th>Reserve?</th>
                <th>BIN / Offer</th>
                <th>QA</th>
                <th>Auction</th>
                <th>State</th>
                <th>User Name</th>
                <th>Owner</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($listings as $l)
            <tr>

                <td>
                    <input type="checkbox" />
                </td>

                <td>{{ $l['ref'] }}</td>

                <td>{{ $l['vehicle'] }}</td>

                <td>{{ $l['vrm'] }}</td>

                <td>{{ number_format($l['mileage']) }}</td>

                <td>
                    £{{ number_format($l['valuation']) }}
                </td>

                <td>
                    @if($l['reserve'])
                        <span class="text-green-600">✔</span>
                    @else
                        <span class="text-red-600">✖</span>
                    @endif
                </td>

                <td>
                    @if($l['bin_enabled'])
                        BIN
                    @elseif($l['offer_enabled'])
                        Offer
                    @else
                        Off
                    @endif
                </td>

                <td>{{ $l['qa'] }}</td>

                <td>
                    {{ $l['auction_code'] ?? '—' }}
                </td>

                <td>
                    <span class="badge">
                        {{ $l['state'] }}
                    </span>
                </td>

                <td>
                    {{ $l['user_name'] }}
                </td>

                <td>
                    {{ $l['owner'] }}
                </td>

                <td>
                    <div class="flex flex-wrap gap-1">

                        <button class="btn btn-primary open-detail">
                            Open
                        </button>

                        <button class="btn quick-view">
                            Quick View
                        </button>

                        <button class="btn assign-owner">
                            Assign Owner
                        </button>

                        <button class="btn mark-qa">
                            Mark QA
                        </button>

                        <button class="btn create-auction">
                            Create Auction
                        </button>

                        <button class="btn enable-bin">
                            Enable BIN
                        </button>

                        <button class="btn">
                            More...
                        </button>

                    </div>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- Footer -->
<div class="flex flex-col md:flex-row items-center justify-between gap-3 mt-4">

    <div class="flex gap-2">

        <button class="kt-btn kt-btn-mono">
            Create Listing
        </button>

        <select class="kt-input">
            <option>Bulk Actions</option>
            <option>Assign Owner</option>
            <option>Mark QA</option>
            <option>Create Auction</option>
            <option>Enable BIN</option>
        </select>

    </div>

    <div class="flex items-center gap-2">

        <button class="kt-btn kt-btn-ghost">1</button>
        <button class="kt-btn kt-btn-ghost">2</button>
        <button class="kt-btn kt-btn-ghost">3</button>

        <span>...</span>

    </div>

</div>