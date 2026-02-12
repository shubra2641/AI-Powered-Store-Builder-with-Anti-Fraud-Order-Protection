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

        @if(!empty($content['plans']))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($content['plans'] as $plan)
                    <div class="pricing-card p-10 rounded-2xl relative flex flex-col h-full transition-all duration-300 {{ !empty($plan['featured']) ? 'shadow-2xl scale-105 z-10 border-2 border-primary' : 'shadow-lg border border-white/5' }}">
                        @if(!empty($plan['featured']))
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary px-4 py-1 rounded-full text-white text-xs font-black uppercase">Most Popular</div>
                        @endif

                        @if(!empty($plan['name']))
                            <h4 class="text-xl font-bold mb-2">{{ $plan['name'] }}</h4>
                        @endif

                        @if(!empty($plan['price']))
                            <div class="flex items-baseline gap-1 mb-6">
                                <span class="text-3xl font-black">${{ $plan['price'] }}</span>
                                <span class="opacity-60 text-sm">/month</span>
                            </div>
                        @endif

                        @if(!empty($plan['features']))
                            <ul class="space-y-4 mb-10 flex-1">
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-center gap-3 text-sm opacity-90">
                                        <i class="fas fa-check-circle text-primary"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($plan['btn_text']))
                            <a href="{{ $plan['btn_url'] ?? '#' }}" class="btn-pricing w-full font-bold py-3 rounded-lg text-center transition-all hover:brightness-110">
                                {{ $plan['btn_text'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
