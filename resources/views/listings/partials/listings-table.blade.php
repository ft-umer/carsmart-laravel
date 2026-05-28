<div id="view-listings">

    <div class="card rounded-lg border border-border overflow-auto">

        <table class="w-full min-w-[1000px] text-sm">

            <thead class="bg-muted/40">

                <tr>
                    <th class="p-3"></th>
                    <th class="p-3 text-left">Listing</th>
                    <th class="p-3 text-left">Vehicle</th>
                    <th class="p-3 text-left">VRM</th>
                    <th class="p-3 text-left">Mileage</th>
                    <th class="p-3 text-right">Guide</th>
                    <th class="p-3 text-left">Reserve?</th>
                    <th class="p-3 text-left">QA</th>
                    <th class="p-3 text-left">State</th>
                    <th class="p-3 text-left">Owner</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-border">

                @foreach($listings as $listing)

                <tr class="hover:bg-muted/5">

                    <td class="p-3">
                        <input type="checkbox">
                    </td>

                    <td class="p-3">
                        <div class="font-medium">
                            {{ $listing['id'] }}
                        </div>
                    </td>

                    <td class="p-3">
                        {{ $listing['vehicle'] }}
                    </td>

                    <td class="p-3">
                        {{ $listing['vrm'] }}
                    </td>

                    <td class="p-3">
                        {{ number_format($listing['mileage']) }}
                    </td>

                    <td class="p-3 text-right">
                        £{{ number_format($listing['guide']) }}
                    </td>

                    <td class="p-3">
                        {{ $listing['reserve'] ? '✔' : '✖' }}
                    </td>

                    <td class="p-3">
                        {{ $listing['qa'] }}
                    </td>

                    <td class="p-3">
                        {{ $listing['state'] }}
                    </td>

                    <td class="p-3">
                        {{ $listing['owner'] }}
                    </td>

                    <td class="p-3">

                        <div class="flex gap-2 justify-end">

                            <button
                                class="kt-btn kt-btn-sm kt-btn-ghost open-detail">
                                Open
                            </button>

                            <button
                                class="kt-btn kt-btn-sm kt-btn-outline quick-view">
                                Quick
                            </button>

                            <button
                                class="kt-btn kt-btn-sm kt-btn-outline">
                                Pull
                            </button>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>