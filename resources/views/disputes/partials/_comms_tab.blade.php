{{-- resources/views/disputes/partials/_comms_tab.blade.php --}}
{{-- Phase 4 — S2: Dispute Case — Communications tab --}}
<div class="space-y-4">

    {{-- Compose --}}
    <div class="flex items-center justify-between">
        <h4 class="font-semibold text-sm">Case communications</h4>
        <button onclick="toggleCompose()" class="kt-btn kt-btn-primary kt-btn-sm">
            <i data-lucide="send" class="w-3.5 h-3.5 mr-1"></i> Send message
        </button>
    </div>

    {{-- Compose panel --}}
    <div id="dispute-compose" class="hidden card border border-border rounded-xl p-4 space-y-3">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label text-xs">To</label>
                <select class="kt-select w-full kt-select-sm">
                    <option>Seller (John Smith)</option>
                    <option>Buyer (Fast Cars Ltd)</option>
                    <option>Both parties</option>
                    <option>Internal only</option>
                </select>
            </div>
            <div>
                <label class="form-label text-xs">Channel</label>
                <select class="kt-select w-full kt-select-sm">
                    <option>Email</option><option>SMS</option><option>WhatsApp</option>
                </select>
            </div>
        </div>
        <div>
            <label class="form-label text-xs">Template</label>
            <select class="kt-select w-full kt-select-sm">
                <option value="">None</option>
                <option>Dispute acknowledgement</option>
                <option>Evidence request</option>
                <option>Decision notification</option>
                <option>Case closed</option>
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Message</label>
            <textarea rows="4" class="kt-textarea w-full text-sm"
                placeholder="Dear {{first_name}}, we are writing regarding dispute DSP-1180…"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="toggleCompose()" class="kt-btn kt-btn-outline kt-btn-sm">Cancel</button>
            <button class="kt-btn kt-btn-primary kt-btn-sm">Send</button>
        </div>
    </div>

    {{-- Thread --}}
    <div class="space-y-3">
        @php
        $msgs = [
            ['from'=>'System','dir'=>'out','channel'=>'Email','time'=>'3 Oct 09:00','text'=>'Dispute acknowledgement sent to both parties. Case reference: DSP-1180.','internal'=>true],
            ['from'=>'John Smith (Seller)','dir'=>'in','channel'=>'Email','time'=>'3 Oct 11:30','text'=>'I would like to raise a concern about the condition of the vehicle at handover. There was a scratch on the front bumper that was not present when I handed over the keys.','internal'=>false],
            ['from'=>'AM (Disputes)','dir'=>'out','channel'=>'Email','time'=>'4 Oct 09:15','text'=>'Thank you for your message, John. We have opened a formal dispute case (DSP-1180) and are requesting evidence from both parties. Please upload photos of the vehicle condition from the handover.','internal'=>false],
            ['from'=>'Fast Cars Ltd (Buyer)','dir'=>'in','channel'=>'Email','time'=>'4 Oct 14:00','text'=>'The scratch was clearly present before we collected the vehicle. We have attached our collection agent\'s photos.','internal'=>false],
            ['from'=>'AM (Disputes)','dir'=>'internal','channel'=>'Internal','time'=>'5 Oct 10:00','text'=>'Reviewing photos from both sides. Seller\'s photos from listing shoot do not show scratch; buyer agent photos at collection show it. Decision leaning towards price adjustment.','internal'=>true],
        ];
        @endphp

        @foreach ($msgs as $m)
            <div class="flex gap-3 {{ $m['dir'] === 'out' ? 'flex-row-reverse' : '' }}">
                <div class="w-7 h-7 rounded-full {{ $m['dir'] === 'internal' ? 'bg-warning/20' : ($m['dir'] === 'out' ? 'bg-primary/20' : 'bg-muted') }} flex-shrink-0 flex items-center justify-center text-xs font-medium {{ $m['dir'] === 'out' ? 'text-primary' : '' }}">
                    {{ substr($m['from'], 0, 1) }}
                </div>
                <div class="max-w-[80%]">
                    <div class="rounded-lg px-3 py-2 text-sm
                        {{ $m['dir'] === 'internal' ? 'bg-warning/10 border border-warning/20' : ($m['dir'] === 'out' ? 'bg-primary/10 border border-primary/20' : 'bg-muted/40') }}">
                        @if ($m['internal'])
                            <p class="text-xs font-medium text-warning-foreground mb-1">
                                <i data-lucide="lock" class="w-3 h-3 inline mr-0.5"></i> Internal note
                            </p>
                        @endif
                        <p>{{ $m['text'] }}</p>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1 {{ $m['dir'] === 'out' ? 'text-right' : '' }}">
                        {{ $m['from'] }} · {{ $m['channel'] }} · {{ $m['time'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

</div>

<script>
function toggleCompose(){
    document.getElementById('dispute-compose').classList.toggle('hidden');
}
</script>
