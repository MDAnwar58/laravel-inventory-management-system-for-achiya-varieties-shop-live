@props([
'landing_page' => null,
'features' => [],
])
<section id="features" class="features">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-4 fw-bold mb-4">{{ $landing_page->features_title ?? 'Powerful Features for' }} <span class="gradient-text">{{ $landing_page->feature_title_part_2 ?? 'Modern Businesses' }}</span></h2>
                <p class="lead">{{ $landing_page->features_sub_title ?? 'Experience the next generation of inventory management with cutting-edge technology and intuitive design.' }}</p>
            </div>
        </div>
        <div class="row g-4">
            @if($features->count() > 0)
            @foreach($features as $feature)
            @if($feature->type == 'analiytics')
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h4 class="mb-3">{{ $feature->title }}</h4>
                    <p>{{ $feature->content }}</p>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h4 class="mb-3">Analytics</h4>
                    <p>Use analytics to track sales trends, monitor stock movement, and gain insights that help optimize inventory and improve overall efficiency.</p>
                </div>
            </div>
            @endif

            @if($features->count() > 0)
            @foreach($features as $feature)
            @if($feature->type === 'real-time-sync')
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h4 class="mb-3">{{ $feature->title }}</h4>
                    <p>{{ $feature->content }}</p>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h4 class="mb-3">Real-Time Sync</h4>
                    <p>Keep your inventory updated instantly across all devices, ensuring accurate stock levels and smooth operations.</p>
                </div>
            </div>
            @endif


            @if($features->count() > 0)
            @foreach($features as $feature)
            @if($feature->type === 'receipt-printer')
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h4 class="mb-3">{{ $feature->title }}</h4>
                    <p>{{ $feature->content }}</p>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h4 class="mb-3">Receipt Printer</h4>
                    <p>Print receipts instantly for sales, returns, and inventory updates, keeping your records accurate and organized.</p>
                </div>
            </div>
            @endif

            @if($features->count() > 0)
            @foreach($features as $feature)
            @if($feature->type === 'advenced-reporting')
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="mb-3">{{ $feature->title }}</h4>
                    <p>{{ $feature->content }}</p>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="mb-3">Advanced Reporting</h4>
                    <p>Generate detailed reports on sales, stock levels, and inventory performance to make informed business decisions.</p>
                </div>
            </div>
            @endif

            @if($features->count() > 0)
            @foreach($features as $feature)
            @if($feature->type === 'smart-alert')
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4 class="mb-3">{{ $feature->title }}</h4>
                    <p>{{ $feature->content }}</p>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4 class="mb-3">Smart Alerts</h4>
                    <p>Get instant notifications when stock levels run low to prevent shortages.</p>
                </div>
            </div>
            @endif

            @if($features->count() > 0)
            @foreach($features as $feature)
            @if($feature->type === 'inventory-hub')
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h4 class="mb-3">{{ $feature->title }}</h4>
                    <p>{{ $feature->content }}</p>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card glass-card">
                    <div class="feature-icon">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h4 class="mb-3">Inventory Hub</h4>
                    <p>Centralize your operations, keeping stock, sales, and updates organized and easy to access.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
