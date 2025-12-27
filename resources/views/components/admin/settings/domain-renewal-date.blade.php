@props([
'domain_renewal_date' => null
])
<div class="d-flex justify-content-between align-items-center px-4 pt-1">
    <div>
        <h5 class="text-secondary fw-semibold">Domian Renewal Date</h5>
    </div>

    <div class="text-secondary">{{ $domain_renewal_date }}</div>
</div>
