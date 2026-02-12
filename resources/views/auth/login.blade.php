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

    <!-- Auth Card -->
    <div class="glass-card p-4 p-md-5">
        <div class="vstack align-center mb-6">
            <h2 class="fs-xl font-800 text-white mb-2">{{ __('auth.login_title') }}</h2>
            <p class="text-muted fs-sm m-0 text-center lh-relaxed">{{ __('auth.welcome_back_ai_nexus') }}</p>
        </div>
        
        <form action="{{ route('login') }}" method="POST" class="vstack gap-4">
            @csrf
            
            <!-- Email Field -->
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

            <!-- Password Field -->
            <div class="vstack gap-2">
                <div class="d-flex justify-between align-center">
                    <label class="form-label-premium m-0">{{ __('auth.password_label') }}</label>
                    <a href="{{ route('password.request') }}" class="text-primary fs-xs font-700 underline-none hover-scale">
                        {{ __('auth.forgot_password_link') }}
                    </a>
                </div>
                <div class="position-relative">
                    <input type="password" name="password" required 
                           class="input-premium p-3 @error('password') is-invalid @enderror" 
                           placeholder="••••••••">
                </div>
                @error('password')
                    <span class="ds-form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="hstack gap-2 align-center mt-1">
                <input type="checkbox" name="remember" id="remember" class="ds-checkbox"> 
                <label for="remember" class="pointer fs-sm text-muted font-600">
                    {{ __('auth.remember_me') }}
                </label>
            </div>

            {!! captcha_render_widget() !!}
            @error('g-recaptcha-response')
                <span class="ds-form-error text-center mt-2 d-block w-full">{{ $message }}</span>
            @enderror

            <!-- Submit Button -->
            <button type="submit" class="btn-gradient w-full py-3 fs-md font-800 mt-2">
                <i class="fas fa-sign-in-alt me-2"></i> {{ __('auth.login_btn') }}
            </button>
        </form>


        <!-- Footer Actions -->
        <div class="vstack align-center mt-5 pt-4 border-top-white-5">
            <p class="text-muted fs-sm m-0">
                {{ __("auth.no_account") }} 
                <a href="{{ route('register') }}" class="text-primary font-800 underline-none ms-1 hover-scale">
                    {{ __('auth.register_now') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
