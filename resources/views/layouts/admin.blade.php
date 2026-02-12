<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ds_settings->get('site_name', config('app.name', 'DropSaaS')) }} - {{ __('admin.admin') }}</title>
    @if($ds_settings->faviconUrl())
        <link rel="icon" type="image/x-icon" href="{{ $ds_settings->faviconUrl() }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/ds-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ds-modal.css') }}">
    
    @stack('styles')

    <script>
        window.APP_URL = "{{ url('/') }}";
    </script>
</head>
<body class="text-white {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

    <div class="bg-effects-container">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        @for($i=1; $i<=10; $i++)
            <div class="particle particle-{{ $i }}"></div>
        @endfor
    </div>

    @if(session('ds_impersonate_admin_id'))
        <div style="background-color: #dc2626; color: white; text-align: center; padding: 0.5rem; font-weight: bold; position: relative; z-index: 9999; display: flex; justify-content: center; align-items: center; gap: 1rem;">
            <span>{{ __('admin.impersonating_notice') }}</span>
            <form action="{{ route('admin.stop-impersonating') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background-color: white; color: #dc2626; padding: 0.25rem 0.75rem; border-radius: 0.25rem; border: none; cursor: pointer; font-weight: bold;">
                    {{ __('admin.stop_impersonating') }}
                </button>
            </form>
        </div>
    @endif

    <div class="admin-layout relative z-10">

        <div class="sidebar-overlay" data-ds-toggle="sidebar"></div>
        
        @include('admin.partials.sidebar')


        <main class="admin-content">

            @include('admin.partials.header')

            @yield('content')
        </main>
    </div>


    @include('components.confirmationModal')



    @include('components.toast')


    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>


    @include('partials.profile_modal')


    <div id="ds-global-loader" class="ds-modal-overlay hidden justify-center align-center">
        <div class="glass-card p-4 d-flex flex-column align-center gap-3">
            <div class="ds-spinner"></div>
            <span class="text-premium">{{ __('admin.processing') }}...</span>
        </div>
    </div>


    <script src="{{ asset('assets/js/admin-main.js') }}"></script>
    @stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Typing Effect
        const texts = [
            "{{ __('admin.discover_ai_power') }}",
            "{{ __('admin.create_pro_content') }}",
            "{{ __('admin.analyze_data_smartly') }}",
            "{{ __('admin.program_efficiently') }}"
        ];
        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        const typingElement = document.getElementById('typing-text');

        function typeEffect() {
            const currentText = texts[textIndex];
            
            if (isDeleting) {
                typingElement.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;
            } else {
                typingElement.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;
            }

            let typeSpeed = isDeleting ? 30 : 50;

            if (!isDeleting && charIndex === currentText.length) {
                typeSpeed = 2000;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % texts.length;
                typeSpeed = 500;
            }

            setTimeout(typeEffect, typeSpeed);
        }

        typeEffect();

        // Reveal animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-active');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.service-card, .glass-card').forEach(card => {
            card.classList.add('reveal-item');
            observer.observe(card);
        });
    });
</script>
 <script src="{{ asset('vendor/tailwind/tailwind.js') }}"></script>
</body>
</html>
