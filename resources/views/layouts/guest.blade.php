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
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/ds-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/ds-modal.css') }}">
    
    @stack('styles')

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

    <div class="admin-layout relative z-10 d-flex align-center justify-center w-full min-h-screen">
        <!-- Main Content -->
        <main class="w-full d-flex align-center justify-center p-3">
            @yield('content')
        </main>
    </div>
        
    <!-- Scripts -->
    @include('components.toast')
    @stack('scripts')
</body>
</html>
