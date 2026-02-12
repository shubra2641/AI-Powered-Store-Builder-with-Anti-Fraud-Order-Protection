{!! $css !!}

<footer class="{{ $attributes['class'] ?? 'py-20 border-t border-white/5' }}" id="{{ $id }}">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <!-- Brand -->
            <div class="col-span-1 lg:col-span-1">
                @if(!empty($content['brand_name']))
                    <a href="/" class="flex items-center gap-2 mb-6">
                        <span class="text-xl font-black tracking-tighter">
                            {{ $content['brand_name'] }}
                        </span>
                    </a>
                @endif
                
                @if(!empty($content['description']))
                    <p class="opacity-60 text-sm leading-relaxed mb-6">
                        {{ $content['description'] }}
                    </p>
                @endif

                @if(!empty($content['socials']))
                    <div class="flex items-center gap-4">
                        @foreach($content['socials'] as $social)
                            <a href="{{ $social['url'] }}" class="glass-card w-10 h-10 flex items-center justify-center rounded-full hover:bg-primary hover:text-white transition-all">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Links -->
            @if(!empty($content['link_groups']))
                @foreach($content['link_groups'] as $group)
                    <div>
                        @if(!empty($group['title']))
                            <h5 class="footer-title text-xs font-black mb-6 uppercase tracking-widest">{{ $group['title'] }}</h5>
                        @endif
                        @if(!empty($group['links']))
                            <ul class="space-y-4">
                                @foreach($group['links'] as $link)
                                    <li><a href="{{ $link['url'] }}" class="opacity-60 text-sm hover:opacity-100 transition-colors">{{ $link['label'] }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="opacity-50 text-xs font-semibold">
                &copy; {{ date('Y') }} {{ $content['brand_name'] ?? config('app.name') }}. All rights reserved.
            </p>
            <div class="flex items-center gap-6">
                <a href="#" class="opacity-50 text-xs font-semibold hover:opacity-100 transition-colors">Privacy Policy</a>
                <a href="#" class="opacity-50 text-xs font-semibold hover:opacity-100 transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
