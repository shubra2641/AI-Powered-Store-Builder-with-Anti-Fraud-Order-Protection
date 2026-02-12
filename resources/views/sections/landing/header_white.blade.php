{!! $css !!}

<header class="shadow-sm fixed w-full z-50 top-0" id="{{ $id }}">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center {{ $isRtl ? 'flex-row-reverse' : '' }}">
        <a href="/" class="text-2xl font-black tracking-wider flex items-center gap-2 {{ $isRtl ? 'flex-row-reverse' : '' }}">
            <svg class="w-8 h-8 text-primary {{ $isRtl ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <span class="flex items-center gap-2 {{ $isRtl ? 'flex-row-reverse' : '' }} brand-text">
                @if(!empty($content['brand_name']))
                    {{ $content['brand_name'] }} 
                @endif
                @if(!empty($content['brand_sub']))
                    <span class="opacity-60 text-lg font-medium">{{ $content['brand_sub'] }}</span>
                @endif
            </span>
        </a>

        @if(!empty($content['menu_items']))
            <nav class="hidden md:flex {{ $isRtl ? 'space-x-8 space-x-reverse' : 'space-x-8' }}">
                @foreach($content['menu_items'] as $item)
                    <a href="{{ $item['url'] }}" class="nav-link hover:text-primary transition font-semibold">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        @endif

        <div class="flex items-center gap-4 {{ $isRtl ? 'flex-row-reverse' : '' }}">
            @if(!empty($content['cta_text']))
                <a href="{{ $content['cta_url'] ?? '#contact' }}" class="hidden md:inline-block btn-cta px-6 py-2 rounded-full font-bold transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    {{ $content['cta_text'] }}
                </a>
            @endif

            <button class="md:hidden opacity-70 focus:outline-none" onclick="document.getElementById('mobile-menu-white').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu-white" class="hidden md:hidden bg-white border-t border-gray-100 p-6 {{ $isRtl ? 'text-right' : 'text-left' }}">
        <div class="flex flex-col gap-4">
            @if(!empty($content['menu_items']))
                @foreach($content['menu_items'] as $item)
                    <a href="{{ $item['url'] }}" class="text-gray-600 font-semibold hover:text-primary">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            @endif
            @if(!empty($content['cta_text']))
                <a href="{{ $content['cta_url'] ?? '#contact' }}" class="btn-cta text-center py-3 rounded-full font-bold mt-2">
                    {{ $content['cta_text'] }}
                </a>
            @endif
        </div>
    </div>
</header>
