{!! $css !!}

<header class="{{ $attributes['class'] ?? 'sticky top-0 z-50 transition-all duration-300 backdrop-blur-md' }}" id="{{ $id }}">
    <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between {{ $isRtl ? 'flex-row-reverse' : '' }}">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center gap-2">
                    @if(!empty($content['logo']))
                        <img src="{{ $content['logo'] }}" alt="Logo" class="h-8 w-auto">
                    @endif
                    @if(!empty($content['brand_name']))
                        <span class="text-xl font-black tracking-tighter brand-text">
                            {{ $content['brand_name'] }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Navigation -->
            @if(!empty($content['menu_items']))
                <nav class="hidden md:flex items-center gap-8">
                    @foreach($content['menu_items'] as $item)
                        <a href="{{ $item['url'] ?? '#' }}" class="nav-link text-sm font-bold opacity-80 hover:opacity-100 transition-colors">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            @endif

            <!-- Actions -->
            <div class="flex items-center gap-4">
                @if(!empty($content['cta_text']))
                    <a href="{{ $content['cta_url'] ?? '#' }}" class="btn-cta rounded-full px-6 py-2 text-sm font-bold shadow-lg transition-transform hover:scale-105 active:scale-95">
                        {{ $content['cta_text'] }}
                    </a>
                @endif
                
                <!-- Mobile Toggle -->
                <button type="button" class="md:hidden opacity-80 p-2" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-black/90 p-6 {{ $isRtl ? 'text-right' : 'text-left' }}">
        <div class="flex flex-col gap-4">
            @if(!empty($content['menu_items']))
                @foreach($content['menu_items'] as $item)
                    <a href="{{ $item['url'] ?? '#' }}" class="text-white font-bold opacity-80 hover:opacity-100">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</header>
