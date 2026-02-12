
            <div class="top-bar flex-column-mobile">
                <div class="d-flex align-center gap-3">
                    <div class="mobile-toggle" data-ds-toggle="sidebar">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div class="welcome-section">
                        <h2 class="fs-xl font-800 m-0">{{ __('admin.welcome_to') }} <span class="gradient-text">{{ $ds_settings->get('site_name', 'DropSaaS') }}</span></h2>
                        <p class="text-muted typing-cursor fs-xs mb-0" id="typing-text">@lang('admin.discover_ai_power')</p>
                    </div>
                </div>
                <div class="d-flex align-center gap-3 justify-between-mobile w-full-mobile">

                    <div class="dropdown-container">
                        <div class="lang-switcher cursor-pointer d-flex align-center gap-2" data-ds-toggle="langMenu">
                            @if($currentLang)
                                <div class="avatar-circle avatar-primary avatar-30">
                                    {{ substr($currentLang->code ?? 'en', 0, 2) }}
                                </div>
                                <span class="fs-xs font-600 uppercase">{{ app()->getLocale() }}</span>
                            @endif
                        </div>
                        <div id="langMenu" class="dropdown-menu lang-dropdown">
                            @foreach($availableLanguages as $lang)
                                <a href="{{ route('language.switch', $lang->code) }}" class="dropdown-item d-flex align-center gap-2">
                                    <div class="avatar-circle {{ $loop->index % 2 == 0 ? 'avatar-primary' : 'avatar-secondary' }} avatar-25">
                                        {{ substr($lang->code, 0, 2) }}
                                    </div>
                                    <span class="fs-xs">{{ $lang->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>


                    <div class="dropdown-container">
                        <div class="notif-trigger-box glass-card hover:bg-primary-soft transition-all cursor-pointer" data-ds-toggle="notifMenu">
                            <i class="far fa-bell fs-lg text-muted"></i>
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                                <span class="position-absolute top-0 end-0 bg-danger border-circle badge-small">{{ auth()->user()->unreadNotifications()->count() > 99 ? '99+' : auth()->user()->unreadNotifications()->count() }}</span>
                            @endif
                        </div>
                        <div id="notifMenu" class="dropdown-menu notif-dropdown-menu">
                            <div class="px-4 py-3 border-bottom border-white-5 mb-2 d-flex justify-between align-center">
                                <h4 class="fs-sm font-800 m-0 gradient-text">{{ __('auth.notifications') }}</h4>
                                @if(auth()->user()->unreadNotifications()->count() > 0)
                                    <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="fs-2xs text-primary bg-none border-none cursor-pointer hover-underline font-700">{{ __('admin.mark_all_read') }}</button>
                                    </form>
                                @endif
                            </div>
                            <div class="activity-feed-dropdown px-2">
                                @forelse(auth()->user()->unreadNotifications()->limit(5)->get() as $notification)
                                    <div class="dropdown-item p-3 border-radius-sm mb-1 d-flex gap-3 {{ $notification->read_at ? '' : 'bg-primary-soft-hover' }}">
                                        <div class="avatar-circle avatar-36 type-{{ $notification->data['type'] ?? 'info' }}">
                                            <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }}"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="m-0 fs-xs font-700 text-white truncate-1 mw-180">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                            <p class="text-muted fs-2xs m-0 mt-1 opacity-75">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="vstack align-center justify-center py-5 opacity-50">
                                        <div class="glass-card-circle mb-3">
                                            <i class="far fa-bell-slash fs-sm"></i>
                                        </div>
                                        <p class="text-muted fs-2xs m-0 font-700">{{ __('admin.no_notifications') }}</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="border-top border-white-5 mt-2 pt-2">
                                <a href="{{ route('admin.notifications.index') }}" class="dropdown-item justify-center text-primary fs-xs font-800">
                                    {{ __('auth.view_all') }}
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="dropdown-container">
                        <div class="d-flex align-center gap-2 cursor-pointer glass-card px-2 py-1 profile-trigger" data-ds-toggle="userMenu">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=8b5cf6&color=fff" class="border-circle img-32">
                            <span class="fs-sm me-1">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down fs-xs text-muted"></i>
                        </div>
                        <div id="userMenu" class="dropdown-menu user-dropdown">
                            <div class="px-3 py-3 border-bottom border-white-5 mb-2 d-flex align-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=8b5cf6&color=fff" class="border-circle img-40">
                                <div>
                                    <p class="m-0 fs-sm font-700">{{ auth()->user()->name }}</p>
                                    <p class="text-muted fs-xs m-0">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                            <button type="button" class="dropdown-item bg-none border-none w-full text-start cursor-pointer" data-ds-modal-open="profileModal">
                                <i class="fas fa-user"></i>
                                <span>{{ __('auth.profile') }}</span>
                            </button>
                            <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                <span>{{ __('auth.sidebar_settings') }}</span>
                            </a>
                            <div class="border-top border-white-5 my-2"></div>
                            <a href="#" class="dropdown-item text-danger" data-ds-logout>
                                <i class="fas fa-sign-out-alt"></i>
                                <span>{{ __('auth.logout') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>