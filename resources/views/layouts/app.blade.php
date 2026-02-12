<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $currentLang->direction ?? 'ltr' }}">
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
    

      <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      
      body {
        font-family: 'Cairo', sans-serif;
        background: linear-gradient(135deg, #08080F 0%, #0F0F1A 50%, #1A1A2E 100%);
        min-height: 100vh;
        overflow-x: hidden;
      }

      .glass-card {
        background: rgba(26, 26, 46, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(139, 92, 246, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      }

      .glass-card:hover {
        border-color: rgba(139, 92, 246, 0.5);
        box-shadow: 0 8px 32px rgba(139, 92, 246, 0.2);
      }

      .neon-text {
        text-shadow: 0 0 10px rgba(139, 92, 246, 0.5),
                     0 0 20px rgba(139, 92, 246, 0.3),
                     0 0 30px rgba(139, 92, 246, 0.2);
      }

      .gradient-text {
        background: linear-gradient(135deg, #8B5CF6, #06B6D4, #F59E0B);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        animation: float 8s ease-in-out infinite;
      }

      .orb-1 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, #8B5CF6 0%, transparent 70%);
        top: -100px;
        right: -100px;
        animation-delay: 0s;
      }

      .orb-2 {
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, #06B6D4 0%, transparent 70%);
        bottom: -50px;
        left: -50px;
        animation-delay: 2s;
      }

      .orb-3 {
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, #F59E0B 0%, transparent 70%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation-delay: 4s;
      }

      .service-icon {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(6, 182, 212, 0.2));
        border: 1px solid rgba(139, 92, 246, 0.3);
      }

      .service-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
      }

      .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.1), transparent);
        transition: left 0.5s ease;
      }

      .service-card:hover::before {
        left: 100%;
      }

      .service-card:hover {
        transform: translateY(-10px) scale(1.02);
      }

      .progress-ring {
        transform: rotate(-90deg);
      }

      .progress-ring-circle {
        transition: stroke-dashoffset 0.5s ease;
      }

      .sidebar-item {
        position: relative;
        overflow: hidden;
      }

      .sidebar-item::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: linear-gradient(180deg, #8B5CF6, #06B6D4);
        transition: height 0.3s ease;
      }

      .sidebar-item:hover::after,
      .sidebar-item.active::after {
        height: 70%;
      }

      .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: #8B5CF6;
        border-radius: 50%;
        animation: particle-float 10s infinite linear;
        opacity: 0.6;
      }

      @keyframes particle-float {
        0% {
          transform: translateY(100vh) rotate(0deg);
          opacity: 0;
        }
        10% {
          opacity: 0.6;
        }
        90% {
          opacity: 0.6;
        }
        100% {
          transform: translateY(-100vh) rotate(720deg);
          opacity: 0;
        }
      }

      .typing-cursor::after {
        content: '|';
        animation: blink 1s infinite;
        color: #8B5CF6;
      }

      @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
      }

      .wave-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%238B5CF6' fill-opacity='0.1' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
        background-size: cover;
      }

      .status-dot {
        animation: pulse-dot 2s infinite;
      }

      @keyframes pulse-dot {
        0%, 100% { 
          transform: scale(1);
          opacity: 1;
        }
        50% { 
          transform: scale(1.2);
          opacity: 0.7;
        }
      }

      .chart-bar {
        transition: height 0.5s ease;
      }

      .chart-bar:hover {
        filter: brightness(1.3);
      }
    </style>
        <script src="{{ asset('vendor/tailwind/tailwind.js') }}"></script>

    @if(isset($trackingPixels))
        {!! $trackingPixels !!}
    @endif
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
        

        @include('user.partials.sidebar')


        <main class="admin-content">

            @include('user.partials.header')

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
</body>
</html>
