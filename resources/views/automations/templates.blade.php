{{-- resources/views/automations/templates.blade.php --}}
{{-- Phase 5 — Automations: Message Templates --}}
@extends('layouts.app')
@section('title', 'Templates — Automations')

@section('content')

    @include('partials._retention_banner')

    <div class="kt-container-fixed">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div>
                <h1 class="text-xl font-semibold text-foreground">Message Templates</h1>
                <p class="text-sm text-muted-foreground mt-0.5">Reusable templates for Email, SMS, and WhatsApp journeys</p>
            </div>
            <button class="kt-btn kt-btn-mono" id="btn-new-template">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> New template
            </button>
        </div>

        {{-- Channel tabs --}}
        <div class="flex border-b border-border gap-1 mb-5">
            @foreach (['All', 'Email', 'SMS', 'WhatsApp'] as $ch)
                <button
                    class="tpl-tab px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                           {{ $ch === 'All' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground' }}"
                    data-channel="{{ $ch }}">
                    {{ $ch }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($templates ?? [] as $tpl)
                <div class="card border border-border rounded-xl p-4 hover:border-primary/40 transition-colors tpl-card"
                    data-channel="{{ $tpl['channel'] }}">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="text-sm font-semibold text-foreground">{{ $tpl['name'] }}</div>
                            <div class="flex gap-1 mt-1">
                                <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $tpl['channel'] }}</span>
                                @if ($tpl['requires_approval'] ?? false)
                                    <span class="kt-badge kt-badge-warning kt-badge-xs">Needs approval</span>
                                @endif
                                <span
                                    class="kt-badge kt-badge-{{ $tpl['approved'] ?? false ? 'success' : 'secondary' }} kt-badge-xs">
                                    {{ $tpl['approved'] ?? false ? 'Approved' : 'Draft' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button class="kt-btn kt-btn-ghost kt-btn-xs" title="Edit">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            </button>
                            <button class="kt-btn kt-btn-ghost kt-btn-xs" title="Duplicate">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-muted-foreground line-clamp-3 font-mono bg-muted/30 rounded p-2">
                        {{ $tpl['preview'] ?? 'No preview available.' }}
                    </p>
                    <div class="flex items-center justify-between mt-3 text-xs text-muted-foreground">
                        <span>Used in {{ $tpl['journey_count'] ?? 0 }} journeys</span>
                        <span>{{ \Carbon\Carbon::parse($tpl['updated_at'])->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-muted-foreground">
                    <i data-lucide="file-text" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                    <p class="text-sm font-medium">No templates yet.</p>
                    <button class="mt-2 kt-btn kt-btn-outline kt-btn-sm" id="btn-new-template-empty">Create first
                        template</button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- New/Edit template modal --}}
    <div id="modal-template" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-background rounded-xl shadow-xl w-full max-w-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">New template</h2>
                <button class="tpl-modal-close text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1 font-medium">Template name</label>
                        <input type="text" class="kt-input w-full" placeholder="e.g. Valuation ready — owner alert" />
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1 font-medium">Channel</label>
                        <select class="kt-input w-full" id="tpl-channel-select">
                            <option>Email</option>
                            <option>SMS</option>
                            <option>WhatsApp</option>
                        </select>
                    </div>
                </div>
                <div id="tpl-subject-row">
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Subject line</label>
                    <input type="text" class="kt-input w-full"
                        placeholder="Your valuation is ready — @{{ listing_title }}" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Body</label>
                    <textarea class="kt-input w-full font-mono text-xs" rows="8"
                        placeholder="Hi @{{ first_name }},&#10;&#10;Your valuation for @{{ listing_title }} is ready.&#10;&#10;Amount: £@{{ valuation_amount }}&#10;Source: @{{ valuation_source }}&#10;&#10;Log in to review and apply it."></textarea>
                    <p class="text-xs text-muted-foreground mt-1">
                        <code class="font-mono">@{{ first_name }}</code>
                        <code class="font-mono">@{{ listing_title }}</code>
                        <code class="font-mono">@{{ valuation_amount }}</code>
                        <code class="font-mono">@{{ delta_pounds }}</code>
                    </p>
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" class="kt-checkbox" />
                    Requires compliance approval before use
                </label>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button class="tpl-modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button class="kt-btn kt-btn-outline">Save draft</button>
                <button class="kt-btn kt-btn-mono">Save & submit for approval</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Tab filter
            document.querySelectorAll('.tpl-tab').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.tpl-tab').forEach(b => {
                        b.className = b.className.replace('border-primary text-primary',
                            'border-transparent text-muted-foreground hover:text-foreground');
                    });
                    this.className = this.className.replace(
                        'border-transparent text-muted-foreground hover:text-foreground',
                        'border-primary text-primary');
                    const ch = this.dataset.channel;
                    document.querySelectorAll('.tpl-card').forEach(c => {
                        c.style.display = ch === 'All' || c.dataset.channel === ch ? '' : 'none';
                    });
                });
            });

            // Open modal
            function openTemplateModal() {
                document.getElementById('modal-template').classList.remove('hidden');
                document.getElementById('modal-template').classList.add('flex');
            }
            document.getElementById('btn-new-template')?.addEventListener('click', openTemplateModal);
            document.getElementById('btn-new-template-empty')?.addEventListener('click', openTemplateModal);
            document.querySelectorAll('.tpl-modal-close').forEach(b => b.addEventListener('click', () => {
                document.getElementById('modal-template').classList.add('hidden');
                document.getElementById('modal-template').classList.remove('flex');
            }));

            // Hide subject for SMS/WhatsApp
            document.getElementById('tpl-channel-select')?.addEventListener('change', function() {
                document.getElementById('tpl-subject-row').style.display = this.value === 'Email' ? '' : 'none';
            });
        </script>
    @endpush

@endsection
