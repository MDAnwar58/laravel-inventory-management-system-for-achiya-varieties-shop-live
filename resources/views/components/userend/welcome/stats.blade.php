@props([
'landing_page' => null,
'custmers_count' => 0
])
@php
    if (!function_exists('format_number_short')) {
        function format_number_short($number): string
        {
            if ($number >= 1000000) {
                return 'M+';
            } elseif ($number >= 1000) {
                return 'K+';
            }
            return '+';
        }
    }

@endphp
<section class="stats">
    <div class="container">
        <div class="row justify-content-center">
            {{-- <div class="col-lg-3 col-md-6 stat-item fade-in">
                <div class="stat-wrapper" data-target="99.9">
                    <span class="stat-number">0</span><span class="static-stat">%</span>
                </div>
                <h5>Uptime</h5>
                <p>Reliable cloud infrastructure</p>
            </div> --}}

            <div class="col-lg-3 col-md-6 stat-item fade-in">
                <div class="stat-wrapper" data-target="{{ $custmers_count }}">
                    <span class="stat-number">0</span><span class="static-stat">{{ format_number_short($custmers_count)}}</span>
                </div>
                <h5>Customers</h5>
                <p>Businesses trust our platform</p>
            </div>

            {{-- <div class="col-lg-3 col-md-6 stat-item fade-in">
                <div class="stat-wrapper" data-target="85">
                    <span class="stat-number">0</span><span class="static-stat">%</span>
                </div>
                <h5>Cost Reduction</h5>
                <p>Average inventory cost savings</p>
            </div> --}}

            <div class="col-lg-3 col-md-6 stat-item fade-in">
                <div class="stat-wrapper" data-target="{{ $landing_page->support_hour ?? 0 }}">
                    <span class="stat-number">0</span><span class="static-stat">/24</span>
                </div>
                <h5>Support</h5>
                <p>Always here to help you</p>
            </div>
        </div>
    </div>
</section>
