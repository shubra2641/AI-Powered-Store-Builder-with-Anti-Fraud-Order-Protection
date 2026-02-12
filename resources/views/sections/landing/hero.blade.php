{!! $css !!}

<section class="{{ $attributes['class'] ?? 'relative overflow-hidden' }}" id="{{ $id }}">
    <!-- Background Effects -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/10 blur-[120px] rounded-full"></div>
        <div class="absolute top-[20%] -right-[10%] w-[30%] h-[30%] bg-indigo-500/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="container relative z-10 mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center">
            @if(!empty($content['tagline']))
                <span class="tagline inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6 animate-fade-in shadow-sm">
                    {{ $content['tagline'] }}
                </span>
            @endif

            @if(!empty($content['title']))
                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-6 leading-tight">
                    {!! $content['title'] !!}
                </h1>
            @endif

            @if(!empty($content['subtitle']))
                <p class="text-lg opacity-80 mb-10 leading-relaxed max-w-2xl mx-auto">
                    {{ $content['subtitle'] }}
                </p>
            @endif

            <div class="flex flex-wrap items-center justify-center gap-4">
                @if(!empty($content['primary_btn_text']))
                    <a href="{{ $content['primary_btn_url'] ?? '#' }}" class="btn-primary px-8 py-4 rounded-xl font-black transition-all hover:-translate-y-1 shadow-xl">
                        {{ $content['primary_btn_text'] }}
                    </a>
                @endif

                @if(!empty($content['secondary_btn_text']))
                    <a href="{{ $content['secondary_btn_url'] ?? '#' }}" class="btn-secondary px-8 py-4 rounded-xl font-bold hover:brightness-105 transition-all border border-white/10 shadow-lg">
                        <i class="fas fa-play-circle me-2 opacity-70"></i>
                        {{ $content['secondary_btn_text'] }}
                    </a>
                @endif
            </div>

            <!-- Social Proof / Trusted By -->
            @if(!empty($content['trusted_by']))
                <div class="mt-20 pt-10 border-t border-white/5">
                    @if(!empty($content['trusted_by_text']))
                        <p class="opacity-50 text-xs font-bold uppercase tracking-widest mb-8">{{ $content['trusted_by_text'] }}</p>
                    @endif
                    <div class="flex flex-wrap justify-center items-center gap-12 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                        @foreach($content['trusted_by'] as $logo)
                            <img src="{{ $logo }}" alt="Partner" class="h-6">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
