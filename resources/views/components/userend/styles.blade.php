<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #5a4b9c 0%, #a24ba2 100%);
        --primary-gradient-subtle: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        overflow-x: hidden;
    }

    .gradient-text {
        background: var(--primary-gradient-subtle);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* Buttons */
    .btn-gradient {
        background: var(--secondary-gradient);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-gradient:hover::before {
        left: 100%;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        color: white;
    }

    /* Method 1: Direct gradient background */
    .btn-gradient-primary {
        background: var(--primary-gradient);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    /* Method 2: Override Bootstrap outline-primary with gradient */
    .btn-outline-primary.gradient-bg {
        background: var(--primary-gradient);
        border: 2px solid transparent;
        background-clip: padding-box;
        color: white;
    }

    .btn-outline-primary.gradient-bg:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        color: white;
        transform: translateY(-2px);
    }

    /* Method 3: Gradient border with transparent background that fills on hover */
    .btn-gradient-border {
        background: linear-gradient(white, white) padding-box,
            var(--primary-gradient-subtle) border-box;
        border: 2px solid transparent;
        background-clip: padding-box, border-box;
        color: rgb(95, 50, 200);
        /* #764ba2 */
        transition: all 0.3s ease;
    }

    .btn-gradient-border:hover {
        background: var(--primary-gradient-subtle);
        color: white;
        transform: translateY(-2px);
    }


    /* Hero Section */
    .hero {
        background: var(--primary-gradient-subtle);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    @media only screen and (max-width: 1199.9px) {
        .hero {
            min-height: auto;
            padding: 10rem 0;
        }
    }
    @media only screen and (max-width: 991.9px) {
        .hero {
            padding: 8.5rem 0;
        }
    }
    @media only screen and (max-width: 574.9px) {
        .hero {
            padding: 9.5rem 0 7.5rem 0;
        }
    }
    @media only screen and (max-width: 234.9px) {
        .app-logo {
            display: none;
        }
    }
    /* 
    @media only screen and (max-width: 1199.9px) {
        .hero {
            min-height: 105vh;
        }
    }
    @media only screen and (max-width: 991.9px) {
        .hero {
            min-height: 135vh;
        }
    }

    @media only screen and (max-width: 767.9px) {
        .hero {
            min-height: 129vh;
        }
    }

    @media only screen and (max-width: 364.9px) {
        .hero {
            min-height: 135vh;
        }
    }

    @media only screen and (max-width: 345.9px) {
        .hero {
            min-height: 145vh;
        }
    }

    @media only screen and (max-width: 288.9px) {
        .hero {
            min-height: 150vh;
        }
    }

    @media only screen and (max-width: 249.9px) {
        .hero {
            min-height: 155vh;
        }
    }
 .hero {
            min-height: 155vh;
        }

     */

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .hero h1 {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        animation: slideInUp 1s ease-out;
    }

    .hero p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        animation: slideInUp 1s ease-out 0.2s both;
    }

    .cta-button {
        animation: slideInUp 1s ease-out 0.4s both;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .floating-shapes {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
    }

    .shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .shape:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .shape:nth-child(2) {
        width: 120px;
        height: 120px;
        top: 60%;
        right: 10%;
        animation-delay: 2s;
    }

    .shape:nth-child(3) {
        width: 60px;
        height: 60px;
        bottom: 20%;
        left: 20%;
        animation-delay: 4s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px) rotate(0deg);
        }

        50% {
            transform: translateY(-20px) rotate(180deg);
        }
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    /* Dashboard Preview in hero section */
    .hero-dashboard {
        position: relative;
        animation: slideInRight 1s ease-out 0.6s both;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .dashboard-preview {
        background: #f8f9fa;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .dashboard-mockup {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .mockup-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .mockup-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .dot-red {
        background: #ff5f56;
    }

    .dot-yellow {
        background: #ffbd2e;
    }

    .dot-green {
        background: #27ca3f;
    }

    .mockup-content {
        background: #f8f9fa;
        height: 200px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .chart-bar {
        position: absolute;
        bottom: 20px;
        background: var(--primary-gradient-subtle);
        border-radius: 4px;
        animation: growUp 2s ease-out;
    }

    .bar-1 {
        left: 30px;
        width: 20px;
        height: 60px;
        animation-delay: 0.2s;
    }

    .bar-2 {
        left: 60px;
        width: 20px;
        height: 80px;
        animation-delay: 0.4s;
    }

    .bar-3 {
        left: 90px;
        width: 20px;
        height: 45px;
        animation-delay: 0.6s;
    }

    .bar-4 {
        left: 120px;
        width: 20px;
        height: 70px;
        animation-delay: 0.8s;
    }

    @keyframes growUp {
        from {
            height: 0;
        }

        to {
            height: inherit;
        }
    }

    /* 
    .hero {
        min-height: 100vh;
        background: var(--primary-gradient);
        position: relative;
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateX(0) translateY(0);
        }

        50% {
            transform: translateX(-50px) translateY(-30px);
        }
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero h1 {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        animation: slideInUp 1s ease-out;
    }

    .hero p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        animation: slideInUp 1s ease-out 0.2s both;
    }

    .cta-button {
        background: var(--secondary-gradient);
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        color: white;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        animation: slideInUp 1s ease-out 0.4s both;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        color: white;
    }

    .hero-dashboard {
        position: relative;
        animation: slideInRight 1s ease-out 0.6s both;
    }

    .dashboard-mockup {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        transform: perspective(1000px) rotateY(-15deg);
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
     */


    /* Navigation */
    .navbar {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .navbar-brand {
        font-weight: 800;
        font-size: 1.5rem;
    }

    .nav-link {
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: #667eea !important;
    }

    /* Features Section */
    .features {
        padding: 100px 0;
        background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .feature-card {
        padding: 40px;
        height: 100%;
        text-align: center;
        transition: all 0.3s ease;
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 30px;
        background: var(--primary-gradient-subtle); /* --accent-gradient */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }

    /* Stats Section */
    .stats {
        background: var(--dark-gradient);
        padding: 80px 0;
        color: white;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
    }

    .stat-wrapper {
        font-size: 3rem;
        font-weight: 800;
        background: var(--accent-gradient); /* --accent-gradient */
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Contact */
    .contact-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0;
    }

    .contact-item {
        text-align: center;
        padding: 30px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        height: 100%;
    }

    .contact-item:hover {
        transform: translateY(-10px);
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .contact-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        color: #ffd700;
    }

    .contact-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .contact-details {
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .contact-details p {
        margin-bottom: 0;
    }

    .contact-details a {
        color: white;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .contact-details a:hover {
        color: #ffd700;
    }

    .social-links {
        margin-top: 30px;
    }

    .social-link {
        display: inline-block;
        width: 50px;
        height: 50px;
        line-height: 50px;
        text-align: center;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 50%;
        margin: 0 10px;
        font-size: 1.2rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #ffd700;
        color: #333;
        transform: scale(1.1);
    }

    .section-title {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title h2 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .section-title p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    /* Testimonials */
    .testimonials {
        padding: 100px 0;
        background: var(--primary-gradient);
        color: white;
    }

    .testimonial-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        margin: 20px 0;
    }

    .testimonial-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 20px;
        background: var(--accent-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }

    /* Footer */
    .footer {
        background: #2c3e50;
        color: white;
        padding: 30px 0 30px 30px;
    }

    .footer-links a {
        color: #bdc3c7;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-links a:hover {
        color: white;
    }

    /* Animations */
    .fade-in {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .fade-in.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
        }

        .hero p {
            font-size: 1.1rem;
        }

        .dashboard-mockup {
            transform: none;
        }
    }

</style>
