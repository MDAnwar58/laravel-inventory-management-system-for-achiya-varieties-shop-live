@props([
'domain' => null
])
<div class="d-flex justify-content-between align-items-center px-4 pt-1">
    <div>
        <h5 class="text-secondary fw-semibold">Domian</h5>
    </div>

    <div class="text-secondary">{{ $domain }}</div>
</div>
@if(in_array(auth()->user()->role, ['owner', 'admin', 'super_admin']))
<hr>
@endif
