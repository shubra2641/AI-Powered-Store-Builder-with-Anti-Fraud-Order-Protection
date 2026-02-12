{!! $css !!}

<section class="{{ $attributes['class'] ?? 'relative overflow-hidden' }}" id="{{ $id }}">
    <div class="container mx-auto px-6">
        <div class="relative overflow-hidden rounded-[40px] p-12 md:p-20 text-center">
            <div class="relative z-10 max-w-3xl mx-auto cta-text">
                @if(!empty($content['title']))
                    <h2 class="text-4xl md:text-5xl font-black mb-6 leading-tight">
                        {{ $content['title'] }}
                    </h2>
                @endif
                
                @if(!empty($content['subtitle']))
                    <p class="text-lg md:text-xl opacity-90 mb-10 leading-relaxed">
                        {{ $content['subtitle'] }}
                    </p>
                @endif

                @if(!empty($content['btn_text']))
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ $content['btn_url'] ?? '#contact' }}" class="btn-action px-10 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-2xl active:scale-95 shadow-lg">
                            {{ $content['btn_text'] }}
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Abstract background shape -->
            <div class="absolute inset-0 z-0 bg-gradient-to-r from-white/5 to-black/5">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 blur-[80px] rounded-full"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 blur-[80px] rounded-full"></div>
            </div>
        </div>
    </div>
</section>
