@props([
'domain_registration_date' => null
])
<div class="d-flex justify-content-between align-items-center px-4 pt-1">
    <div>
        <h5 class="text-secondary fw-semibold">Domian Resitration Date</h5>
    </div>

    <div class="text-secondary">{{ $domain_registration_date }}</div>
</div>
<hr>
