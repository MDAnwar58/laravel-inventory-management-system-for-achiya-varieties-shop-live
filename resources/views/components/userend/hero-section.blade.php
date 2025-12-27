@props([
    'landing_page' => null
])
<section class="hero d-flex align-items-center">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content-area" data-aos="fade-right">
                <h1 class="text-white">{{ $landing_page?->hero_title_part_1 ?? 'Smart Inventory' }} <span class="text-warning">{{ $landing_page->hero_title_part_2 ?? 'Revolution' }}</span></h1>
                <p class="text-white-50">{{ $landing_page->short_des ?? 'Transform your business with inventory management. Real-time tracking, predictive analytics, and automated optimization all in one powerful platform.' }}</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-gradient btn-lg text-uppercase cta-button">
                        <i class="fas fa-rocket me-2"></i>Get Start
                    </a>
                </div>
                {{-- <div class="mt-4 d-flex align-items-center gap-4 text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star text-warning me-1"></i>
                        <span>4.9/5 Rating</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users me-1"></i>
                        <span>10,000+ Users</span>
                    </div>
                </div> --}}
            </div>
            <div class="col-lg-6 hero-dashboard mt-lg-0 mt-5">
                <div class="dashboard-preview glass">
                    <div class="dashboard-mockup">
                        <div class="mockup-header">
                            <div class="mockup-dot dot-red"></div>
                            <div class="mockup-dot dot-yellow"></div>
                            <div class="mockup-dot dot-green"></div>
                        </div>
                        <div class="mockup-content">
                            <div class="chart-bar bar-1"></div>
                            <div class="chart-bar bar-2"></div>
                            <div class="chart-bar bar-3"></div>
                            <div class="chart-bar bar-4"></div>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <i class="fas fa-chart-line fa-3x" style="color: #667eea; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- <section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="text-white">Smart Inventory <span class="text-warning">Revolution</span></h1>
                <p class="text-white-50">Transform your business with AI-powered inventory management. Real-time tracking, predictive analytics, and automated optimization all in one powerful platform.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#demo" class="cta-button">
                        <i class="fas fa-rocket me-2"></i>Start Free Trial
                    </a>
                    <a href="#features" class="btn btn-outline-light rounded-pill px-4">Learn More</a>
                </div>
                <div class="mt-4">
                    <small class="text-white-50">
                        <i class="fas fa-check me-2"></i>No credit card required
                        <i class="fas fa-check me-2 ms-3"></i>14-day free trial
                    </small>
                </div>
            </div>
            <div class="col-lg-6 hero-dashboard">
                <div class="dashboard-mockup">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Why Choose Smart Inventory?</h6>
                        <span class="badge bg-success">Live</span>
                    </div>
                    <div class="p-4 bg-dark bg-opacity-25 rounded">

                        <ul class="list-unstyled text-white-50">
                            <li class="mb-2">
                                <i class="fas fa-bolt text-warning me-2"></i>
                                Real-time stock updates
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-robot text-warning me-2"></i>
                                AI-powered demand forecasting
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-boxes text-warning me-2"></i>
                                Multi-warehouse management
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-mobile-alt text-warning me-2"></i>
                                Access anywhere with mobile app
                            </li>
                        </ul>
                        <div class="mt-3">
                            <a href="#features" class="btn btn-warning rounded-pill px-4">
                                Explore Features
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section> --}}
