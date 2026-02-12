{!! $css !!}

<section class="{{ $attributes['class'] ?? 'py-20' }}" id="{{ $id }}">
    <div class="container mx-auto px-6">
        @if(!empty($content['title']) || !empty($content['subtitle']))
            <div class="text-center max-w-3xl mx-auto mb-16">
                @if(!empty($content['title']))
                    <h2 class="text-3xl font-black mb-4">{{ $content['title'] }}</h2>
                @endif
                @if(!empty($content['subtitle']))
                    <p class="opacity-70">{{ $content['subtitle'] }}</p>
                @endif
            </div>
        @endif

        @if(!empty($content['items']))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($content['items'] as $item)
                    <div class="glass-card p-8 group hover:-translate-y-2 transition-all duration-500 border border-white/5">
                        @if(!empty($item['icon']))
                            <div class="feature-icon text-3xl mb-6 group-hover:scale-110 transition-transform">
                                <i class="{{ $item['icon'] }}"></i>
                            </div>
                        @endif
                        @if(!empty($item['title']))
                            <h4 class="text-xl font-bold mb-3">{{ $item['title'] }}</h4>
                        @endif
                        @if(!empty($item['description']))
                            <p class="opacity-60 leading-relaxed">{{ $item['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
