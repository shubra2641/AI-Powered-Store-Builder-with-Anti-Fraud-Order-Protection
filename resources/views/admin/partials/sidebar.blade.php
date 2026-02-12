
        <aside class="sidebar glass-card">
            <div class="sidebar-header mb-6 px-2">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-center gap-3 text-decoration-none">
                    <div class="w-48 h-48 border-radius-sm bg-gradient-elite d-flex align-center justify-center shadow-lg">
                        <i class="fas fa-brain fs-lg text-white"></i>
                    </div>
                    <div>
                        <h1 class="fs-md font-800 m-0 gradient-text">{{ $ds_settings->get('site_name', 'DropSaaS') }}</h1>
                        <p class="text-muted fs-2xs m-0">{{ __('auth.system_status') }}</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1">
                <div class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>{{ __('auth.dashboard') }}</span>
                    </a>
                    
                    <a href="{{ route('admin.languages.index') }}" class="sidebar-item {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                        <i class="fas fa-language"></i>
                        <span>{{ __('admin.languages') }}</span>
                    </a>

                    <a href="{{ route('admin.landing-pages.index') }}" class="sidebar-item {{ request()->routeIs('admin.landing-pages.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ __('admin.landing_pages') }}</span>
                    </a>

                    <a href="{{ route('admin.plans.index') }}" class="sidebar-item {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                        <i class="fas fa-crown"></i>
                        <span>{{ __('admin.plans') }}</span>
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>{{ __('admin.users') }}</span>
                    </a>

                    <a href="{{ route('admin.transactions.index') }}" class="sidebar-item {{ request()->routeIs('admin.transactions.index') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        <span>{{ __('admin.transactions') }}</span>
                    </a>

                    <a href="{{ route('admin.emails.index') }}" class="sidebar-item {{ request()->routeIs('admin.emails.*') ? 'active' : '' }}">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>{{ __('admin.email_templates') }}</span>
                    </a>

                    <a href="{{ route('admin.pages.index') }}" class="sidebar-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract"></i>
                        <span>{{ __('admin.legal_pages') }}</span>
                    </a>

                    <a href="{{ route('admin.integrations.index') }}" class="sidebar-item {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}">
                        <i class="fas fa-plug"></i>
                        <span>{{ __('admin.integrations') }}</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>{{ __('admin.settings') }}</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer mt-auto">
                <a href="#" class="sidebar-item mt-4 text-danger hover-bg-white-5" data-ds-logout>
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ __('auth.logout') }}</span>
                </a>
            </div>
        </aside>
