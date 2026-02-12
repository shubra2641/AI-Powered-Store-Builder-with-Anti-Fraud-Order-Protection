@extends('layouts.guest')

@section('content')
<div class="mw-500 w-full px-3 py-5">
    <!-- Branding / Logo Area -->
    <div class="vstack align-center mb-8">
        <div class="d-flex align-center justify-center mb-4">
            <div class="glass-card p-3 border-circle d-flex align-center justify-center bg-purple-soft w-80 h-80">
                <span class="fs-3xl font-800 gradient-text">DS</span>
            </div>
        </div>
        <h1 class="fs-2xl font-900 text-white m-0 d-flex justify-center align-center">
            Drop<span class="gradient-text">SaaS</span>
        </h1>
    </div>

    <!-- Verification Card -->
    <div class="glass-card p-4 p-md-5">
        <div class="vstack align-center mb-6">
            <div class="glass-card p-3 border-circle d-flex align-center justify-center bg-purple-soft mb-4 w-60 h-60">
                <i class="fas fa-envelope-open-text fs-xl gradient-text"></i>
            </div>
            <h2 class="fs-xl font-800 text-white mb-2">{{ __('auth.verify_email_title') }}</h2>
            <p class="text-muted fs-sm m-0 text-center lh-relaxed">
                {{ __('auth.verify_email_desc') }}
            </p>
        </div>

        @if (session('resent'))
            <div class="glass-card p-3 mb-6 bg-green-soft border-success-soft">
                <div class="hstack gap-3 align-center">
                    <i class="fas fa-check-circle text-success fs-md"></i>
                    <span class="text-success fs-xs font-600">{{ __('auth.verification_link_sent') }}</span>
                </div>
            </div>
        @endif

        <div class="vstack gap-3">
            <form action="{{ route('verification.resend') }}" method="POST" class="w-full">
                @csrf
                
                {!! captcha_render_widget() !!}
                @error('g-recaptcha-response')
                    <span class="ds-form-error text-center mt-2 d-block w-full">{{ $message }}</span>
                @enderror

                <button type="submit" class="btn-gradient w-full py-3 fs-md font-800 mt-2">
                    <i class="fas fa-paper-plane me-2"></i> {{ __('auth.resend_verification_link') }}
                </button>
            </form>
            
            {!! captcha_render_script() !!}

            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="glass-card w-full py-3 fs-md font-700 pointer hover-scale bg-none border-white-10 text-white">
                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('auth.logout') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Support Link -->
    <div class="vstack align-center mt-6">
        <p class="text-muted fs-xs m-0">
            {{ __('admin.thank_you') }}
        </p>
    </div>
</div>
@endsection
