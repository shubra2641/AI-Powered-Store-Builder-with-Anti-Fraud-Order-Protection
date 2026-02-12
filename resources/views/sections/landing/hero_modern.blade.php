{!! $css !!}

<section class="{{ $attributes['class'] ?? 'overflow-hidden' }}" id="{{ $id }}">
    <div class="container mx-auto px-6 flex flex-col-reverse {{ $isRtl ? 'md:flex-row-reverse' : 'md:flex-row' }} items-center gap-12">
        <div class="w-full md:w-1/2 text-center {{ $isRtl ? 'md:text-right' : 'md:text-left' }}">
            @if(!empty($content['tagline']))
                <span class="tagline px-4 py-1.5 rounded-full text-sm font-bold mb-6 inline-block animate-fade-in">
                    {{ $content['tagline'] }}
                </span>
            @endif
            
            @if(!empty($content['title']))
                <h1 class="text-4xl md:text-6xl font-black leading-tight mb-6">
                    {!! $content['title'] !!}
                </h1>
            @endif
            
            @if(!empty($content['subtitle']))
                <p class="text-lg opacity-80 mb-8 leading-relaxed max-w-xl {{ $isRtl ? 'mr-0' : 'ml-0' }} mx-auto md:mx-0">
                    {{ $content['subtitle'] }}
                </p>
            @endif
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center {{ $isRtl ? 'md:justify-start' : 'md:justify-start' }}">
                @if(!empty($content['primary_btn_text']))
                    <a href="{{ $content['primary_btn_url'] ?? '#contact' }}" class="btn-primary px-8 py-3 rounded-lg font-bold text-lg hover:brightness-110 transition text-center shadow-lg transform active:scale-95">
                        {{ $content['primary_btn_text'] }}
                    </a>
                @endif

                @if(!empty($content['secondary_btn_text']))
                    <a href="{{ $content['secondary_btn_url'] ?? '#works' }}" class="btn-secondary border border-gray-300 px-8 py-3 rounded-lg font-bold text-lg hover:bg-gray-50 transition text-center transform active:scale-95">
                        {{ $content['secondary_btn_text'] }}
                    </a>
                @endif
            </div>
        </div>
        
        <div class="w-full md:w-1/2 relative">
            <div class="absolute -top-4 -right-4 w-72 h-72 bg-primary/10 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute -bottom-8 -left-4 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            
            @if(!empty($content['image_url']))
                <div class="relative z-10">
                    <img src="{{ $content['image_url'] }}" 
                         alt="Hero Image" 
                         class="rounded-2xl shadow-2xl transform {{ $isRtl ? '-rotate-1' : 'rotate-1' }} hover:rotate-0 transition duration-500 w-full object-cover aspect-[4/3]">
                </div>
            @endif
        </div>
    </div>
</section>
