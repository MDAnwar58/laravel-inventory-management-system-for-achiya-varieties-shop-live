<script>
    // Fade in animation on scroll
    const observerOptions = {
        threshold: 0.1
        , rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });

    // Chart.js for dashboard mockup
    const chartCanvas = document.getElementById('inventoryChart');
    if (chartCanvas) {
        new Chart(chartCanvas, {
            type: 'bar'
            , data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
                , datasets: [{
                    label: 'Sales'
                    , data: [12, 19, 3, 5, 2, 3]
                    , backgroundColor: [
                        'rgba(102, 126, 234, 0.8)'
                        , 'rgba(102, 126, 234, 0.8)'
                        , 'rgba(102, 126, 234, 0.8)'
                        , 'rgba(102, 126, 234, 0.8)'
                        , 'rgba(102, 126, 234, 0.8)'
                        , 'rgba(102, 126, 234, 0.8)'
                    ]
                    , borderColor: [
                        'rgba(102, 126, 234, 1)'
                        , 'rgba(102, 126, 234, 1)'
                        , 'rgba(102, 126, 234, 1)'
                        , 'rgba(102, 126, 234, 1)'
                        , 'rgba(102, 126, 234, 1)'
                        , 'rgba(102, 126, 234, 1)'
                    ]
                    , borderWidth: 1
                }]
            }
            , options: {
                responsive: true
                , maintainAspectRatio: false
                , scales: {
                    y: {
                        beginAtZero: true
                        , grid: {
                            display: false
                        }
                        , ticks: {
                            display: false
                        }
                    }
                    , x: {
                        grid: {
                            display: false
                        }
                        , ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
                , plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                    , block: 'start'
                });
            }
        });
    });

    // Navbar background change on scroll
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = 'none';
        }
    });

    // Counter animation for stats
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }

            if (element.textContent.includes('%')) {
                element.textContent = Math.floor(current) + '%';
            } else if (element.textContent.includes('K+')) {
                element.textContent = Math.floor(current) + 'K+';
            } else if (element.textContent.includes('/')) {
                element.textContent = '24/7';
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Trigger counter animation when stats section is visible
   function animateCounter(el, target, decimals = 0, duration = 2000) {
    const start = 0;
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1); // 0 → 1
        const value = start + (target - start) * progress;

        el.textContent = value.toFixed(decimals);

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

    const statsSection = document.querySelector('.stats');

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statWrappers = entry.target.querySelectorAll('.stat-wrapper');
            statWrappers.forEach(wrapper => {
                const target = parseFloat(wrapper.dataset.target);
                const statNumber = wrapper.querySelector('.stat-number');
                const decimals = target % 1 !== 0 ? 1 : 0; // 1 decimal if float
                animateCounter(statNumber, target, decimals, 2000); // 2s animation
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

if (statsSection) {
    statsObserver.observe(statsSection);
}


</script>
