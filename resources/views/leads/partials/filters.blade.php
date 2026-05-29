{{-- ─── Filters bar ─────────────────────────────────────────────────────── --}}

<form
    method="GET"
    action="{{ route('leads.index') }}"
    id="filter-form"
    class="card border border-border rounded-lg p-3 mb-5">

    <input type="hidden" name="view" value="{{ $view }}">

    <div class="flex flex-wrap gap-2 items-end">

        {{-- Search --}}
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-muted-foreground mb-1">
                Search
            </label>

            <input
                type="search"
                name="search"
                value="{{ $search }}"
                class="kt-input w-full"
                placeholder="Name, email, phone, VRM…" />
        </div>

        {{-- Stage --}}
        <div>
            <label class="block text-xs text-muted-foreground mb-1">
                Stage
            </label>

            <select name="stage" class="kt-input">
                <option value="">All stages</option>

                @foreach ($stages as $s)
                    <option
                        value="{{ $s }}"
                        {{ $stage === $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Owner --}}
        <div>
            <label class="block text-xs text-muted-foreground mb-1">
                Owner
            </label>

            <select name="owner" class="kt-input">
                <option value="">All owners</option>

                @foreach ($owners as $o)
                    <option
                        value="{{ $o }}"
                        {{ $owner === $o ? 'selected' : '' }}>
                        {{ $o }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Source --}}
        <div>
            <label class="block text-xs text-muted-foreground mb-1">
                Source
            </label>

            <select name="source" class="kt-input">
                <option value="">All sources</option>

                @foreach ($sources as $s)
                    <option
                        value="{{ $s }}"
                        {{ $source === $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- SLA --}}
        <div>
            <label class="block text-xs text-muted-foreground mb-1">
                SLA
            </label>

            <select name="sla" class="kt-input">
                <option value="">Any</option>

                <option
                    value="due_today"
                    {{ $sla === 'due_today' ? 'selected' : '' }}>
                    Due today
                </option>

                <option
                    value="overdue"
                    {{ $sla === 'overdue' ? 'selected' : '' }}>
                    Overdue
                </option>
            </select>
        </div>

        {{-- Buttons --}}
        <button
            type="submit"
            class="kt-btn kt-btn-mono self-end">
            Apply
        </button>

        <a
            href="{{ route('leads.index') }}"
            class="kt-btn kt-btn-ghost self-end">
            Reset
        </a>

    </div>

</form>