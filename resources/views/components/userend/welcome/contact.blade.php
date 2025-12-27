@props([
'landing_page' => null,
'contactInfos' => [],
])
<section id="contact" class="contact-section">
    <div class="container">
        <!-- Section Header -->
        <div class="section-title text-center">
            <h2>{{ $landing_page->contact_title ?? 'Get In Touch' }}</h2>
            <p>{{ $landing_page->contact_sub_title ?? "We'd love to hear from you. Reach out to us through any of these channels." }}</p>
        </div>

        <!-- Contact Information -->
        <div class="row g-4 cards-row">
            <!-- Address -->
            @if($contactInfos->count() > 0)
            @foreach($contactInfos as $contactInfo)
            @if($contactInfo->type === 'address')
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4 class="contact-title">{{ $contactInfo->type }}</h4>
                    <div class="contact-details">
                        {!! $contactInfo->content !!}
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4 class="contact-title">Visit Us</h4>
                    <div class="contact-details">
                        <p>123 Business Street<br>
                            Suite 456<br>
                            New York, NY 10001</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Phone -->
            @if($contactInfos->count() > 0)
            @foreach($contactInfos as $contactInfo)
            @if($contactInfo->type === 'phone')
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4 class="contact-title">{{ $contactInfo->title }}</h4>
                    <div class="contact-details">
                        {!! $contactInfo->content !!}
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4 class="contact-title">Call Us</h4>
                    <div class="contact-details">
                        <p><a href="tel:+1234567890">+1 (234) 567-8900</a><br>
                            <a href="tel:+1234567891">+1 (234) 567-8901</a><br>
                            <small>Mon - Fri: 9AM - 6PM</small></p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Email -->
            @if($contactInfos->count() > 0)
            @foreach($contactInfos as $contactInfo)
            @if($contactInfo->type === 'email')
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 class="contact-title">{{ $contactInfo->title }}</h4>
                    <div class="contact-details">
                        {!! $contactInfo->content !!}
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 class="contact-title">Email Us</h4>
                    <div class="contact-details">
                        <p><a href="mailto:info@company.com">info@company.com</a><br>
                            <a href="mailto:support@company.com">support@company.com</a><br>
                            <small>24/7 Support Available</small></p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Business Hours -->
            @if($contactInfos->count() > 0)
            @foreach($contactInfos as $contactInfo)
            @if($contactInfo->type === 'business_hour')
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="contact-title">{{ $contactInfo->title }}</h4>
                    <div class="contact-details">
                        {!! $contactInfo->content !!}
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @else
            <div class="col-lg-3 col-md-6">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="contact-title">Business Hours</h4>
                    <div class="contact-details">
                        <p><strong>Mon - Fri:</strong> 9:00 AM - 6:00 PM<br>
                            <strong>Saturday:</strong> 10:00 AM - 4:00 PM<br>
                            <strong>Sunday:</strong> Closed</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Social Media Links -->
        <div class="row mt-5">
            {{-- <div class="col-12 text-center">
                <h4 class="mb-4">Follow Us</h4>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="social-link" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div> --}}

            <!-- Map Placeholder -->
            <div class="row">
                <div class="col-12">
                    <div class="map-container">
                        <div class="map-placeholder text-center pt-4">
                            <i class="fas fa-map fa-3x mb-3" style="color: #ffd700;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
