        <!-- Sidebar -->
        <aside class="sidebar glass-card">
            <div class="sidebar-header mb-6 px-2">
                <a href="{{ route('user.dashboard') }}" class="d-flex align-center gap-3 text-decoration-none">
                    <div class="w-48 h-48 border-radius-sm bg-gradient-elite d-flex align-center justify-center shadow-lg">
                        <i class="fas fa-brain fs-lg text-white"></i>
                    </div>
                    <div>
                        <h1 class="fs-md font-800 m-0 gradient-text">AI Nexus</h1>
                        <p class="text-muted fs-2xs m-0">{{ __('auth.system_status') }}</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1">

            </nav>

            <div class="sidebar-footer mt-auto">
                <a href="#" class="sidebar-item mt-4 text-danger hover-bg-white-5" data-ds-logout>
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('auth.logout') }}</span>
                </a>
            </div>
        </aside>
