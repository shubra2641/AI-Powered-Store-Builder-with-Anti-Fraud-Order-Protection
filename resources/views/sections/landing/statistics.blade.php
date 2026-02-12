{!! $css !!}

<section class="{{ $attributes['class'] ?? 'py-16' }}" id="{{ $id }}">
    <div class="container mx-auto px-6">
        <div class="glass-card py-12 px-6 border border-white/5">
            @if(!empty($content['items']))
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($content['items'] as $item)
                        <div class="text-center">
                            @if(!empty($item['value']))
                                <h3 class="stat-value text-3xl md:text-4xl font-black mb-2">{{ $item['value'] }}</h3>
                            @endif
                            @if(!empty($item['label']))
                                <p class="opacity-60 text-xs font-bold uppercase tracking-widest">{{ $item['label'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
