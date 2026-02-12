@extends('layouts.guest')

@section('content')
<div class="mw-400 w-full px-3 py-5">
    <!-- Branding / Logo Area -->
    <div class="vstack align-center mb-8">
        <div class="d-flex align-center justify-center mb-3">
            <div class="glass-card p-3 border-circle d-flex align-center justify-center bg-purple-soft w-80 h-80">
                <span class="fs-2xl font-800 gradient-text">DS</span>
            </div>
        </div>
        <h1 class="fs-xl font-900 text-white m-0 d-flex justify-center align-center">
            Drop<span class="gradient-text">SaaS</span>
        </h1>
    </div>

    <!-- Auth Card -->
    <div class="glass-card p-4 p-md-5">
        <div class="vstack align-center mb-6">
            <h2 class="fs-xl font-800 text-white mb-2">{{ __('auth.reset_password_title') }}</h2>
            <p class="text-muted fs-sm m-0 text-center lh-relaxed">{{ __('auth.password_reset_link_sent') }}</p>
        </div>
        
        <form action="{{ route('password.email') }}" method="POST" class="vstack gap-4">
            @csrf
            
            <div class="vstack gap-2">
                <label class="form-label-premium">{{ __('auth.email_label') }}</label>
                <div class="position-relative">
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input-premium p-3 @error('email') is-invalid @enderror" 
                           placeholder="name@example.com">
                </div>
                @error('email')
                    <span class="ds-form-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-gradient w-full py-3 fs-md font-800 mt-2">
                <i class="fas fa-paper-plane me-2"></i> {{ __('auth.send_reset_link') }}
            </button>
        </form>

        {!! captcha_render_widget() !!}
        @error('g-recaptcha-response')
            <span class="ds-form-error text-center mt-2 d-block w-full">{{ $message }}</span>
        @enderror


        <div class="vstack align-center mt-5 pt-4 border-top-white-5">
            <p class="text-muted fs-sm m-0">
                <a href="{{ route('login') }}" class="text-primary font-800 underline-none hover-scale">
                    <i class="fas fa-arrow-left me-2 fs-xs"></i> {{ __('auth.login_now') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
