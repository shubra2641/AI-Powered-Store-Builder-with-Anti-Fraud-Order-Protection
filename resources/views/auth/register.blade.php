@extends('layouts.guest')

@section('content')
<div class="mw-500 w-full px-3 py-5">
    <!-- Branding / Logo Area -->
    <div class="vstack align-center mb-6">
        <div class="d-flex align-center justify-center mb-3">
            <div class="glass-card p-3 border-circle d-flex align-center justify-center bg-purple-soft w-70 h-70">
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
            <h2 class="fs-xl font-800 text-white mb-2">{{ __('auth.register_title') }}</h2>
            <p class="text-muted fs-sm m-0 text-center lh-relaxed">{{ __('auth.create_content') }}</p>
        </div>
        
        <form action="{{ route('register') }}" method="POST" class="vstack gap-4">
            @csrf
            
            <!-- Full Name -->
            <div class="vstack gap-2">
                <label class="form-label-premium">{{ __('auth.full_name_label') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="input-premium p-3 @error('name') is-invalid @enderror" 
                       placeholder="John Doe">
                @error('name')
                    <span class="ds-form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="vstack gap-2">
                <label class="form-label-premium">{{ __('auth.email_label') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       class="input-premium p-3 @error('email') is-invalid @enderror" 
                       placeholder="name@example.com">
                @error('email')
                    <span class="ds-form-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Grid -->
            <div class="vstack md-grid md-cols-2 gap-4">
                <div class="vstack gap-2">
                    <label class="form-label-premium">{{ __('auth.password_label') }}</label>
                    <input type="password" name="password" required 
                           class="input-premium p-3 @error('password') is-invalid @enderror" 
                           placeholder="••••••••">
                    @error('password')
                        <span class="ds-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="vstack gap-2">
                    <label class="form-label-premium">{{ __('auth.confirm_password_label') }}</label>
                    <input type="password" name="password_confirmation" required 
                           class="input-premium p-3" 
                           placeholder="••••••••">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-gradient w-full py-3 fs-md font-800 mt-2">
                <i class="fas fa-user-plus me-2"></i> {{ __('auth.register_btn') }}
            </button>
        </form>


        <!-- Footer Actions -->
        <div class="vstack align-center mt-5 pt-4 border-top-white-5">
            <p class="text-muted fs-sm m-0">
                {{ __('auth.already_have_account') }} 
                <a href="{{ route('login') }}" class="text-primary font-800 underline-none ms-1 hover-scale">
                    {{ __('auth.login_now') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
