{!! $css !!}

<section id="{{ $id }}" class="relative overflow-hidden py-20 lg:py-32">
    <!-- Background Glows -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/20 blur-[120px] rounded-full"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary/20 blur-[120px] rounded-full"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col {{ $isRtl ? 'lg:flex-row-reverse' : 'lg:flex-row' }} items-center gap-16">
            <!-- Content -->
            <div class="w-full lg:w-1/2 {{ $isRtl ? 'text-right' : 'text-left' }}">
                @if(!empty($content['tagline']))
                    <span class="tagline inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6 shadow-sm">
                        {{ $content['tagline'] }}
                    </span>
                @endif

                @if(!empty($content['title']))
                    <h1 class="text-4xl md:text-6xl font-black leading-tight mb-6">
                        {!! $content['title'] !!}
                    </h1>
                @endif

                @if(!empty($content['subtitle']))
                    <p class="opacity-60 text-lg md:text-xl mb-10 max-w-xl">
                        {{ $content['subtitle'] }}
                    </p>
                @endif

                <div class="flex flex-wrap gap-4 {{ $isRtl ? 'justify-end' : '' }}">
                    @if(!empty($content['primary_btn_text']))
                        <a href="{{ $content['primary_btn_url'] ?? '#' }}" class="btn-primary px-8 py-4 rounded-xl font-black transition-all shadow-xl">
                            {{ $content['primary_btn_text'] }}
                        </a>
                    @endif
                    
                    @if(!empty($content['secondary_btn_text']))
                        <a href="{{ $content['secondary_btn_url'] ?? '#' }}" class="btn-secondary flex items-center gap-2 px-8 py-4 rounded-xl font-bold hover:brightness-105 transition-all border border-white/10 shadow-lg">
                            {{ $content['secondary_btn_text'] }}
                            <i class="fas fa-arrow-right text-xs {{ $isRtl ? 'rotate-180' : '' }}"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Image Side -->
            <div class="w-full lg:w-1/2">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-secondary/20 blur-3xl rounded-full"></div>
                    @if(!empty($content['image_url']))
                        <div class="glass-card p-2 rounded-3xl border border-white/10 relative z-10">
                            <img src="{{ $content['image_url'] }}" 
                                 alt="Product Showcase" 
                                 class="rounded-2xl w-full shadow-2xl">
                        </div>
                    @endif
                    
                    <!-- Floating Badges -->
                    <div class="absolute -top-6 -right-6 glass-card p-4 rounded-2xl border border-white/10 animate-bounce">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center text-green-500">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <p class="text-[10px] opacity-60 uppercase font-black">Growth</p>
                                <p class="font-black">+124%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
