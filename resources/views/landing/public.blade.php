
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $landingPage->translations->first()?->title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $landingPage->translations->first()?->meta_description }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- This page uses Inline and needs it in order to 
    load the full HTML zip file without distortion after loading -->
    
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        [dir="rtl"] .space-x-reverse > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }
    </style>
    
    @if(isset($trackingPixels))
        {!! $trackingPixels !!}
    @endif
</head>
<body class="bg-gray-50 text-gray-800 antialiased overflow-x-hidden">

    <div id="landing-sections">
        @if(isset($cachedHtml) && !empty($cachedHtml))
            {!! $cachedHtml !!}
        @else
            @foreach($sections as $section)
                @if($section['template'])
                    @include($section['template'], $section)
                @endif
            @endforeach
        @endif
    </div>


    @push('scripts')
    <script src="{{ asset('vendor/tailwind/tailwind.js') }}"></script>
    
    @if(!isset($isExport))

        <script>
            window.addEventListener('message', function(event) {
                if (event.data.type === 'UPDATE_CONTENT') {
                    // Notify parent that update started (optional)
                    window.parent.postMessage({ type: 'PREVIEW_UPDATE_START' }, '*');

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('sections', JSON.stringify(event.data.sections));
                    formData.append('is_preview', '1');

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        const landingSections = document.getElementById('landing-sections');
                        if (landingSections) {
                            // Even if html is empty (no sections), we must update and notify
                            landingSections.innerHTML = html || '';
                            
                            // Essential: notifying the parent that we are done is crucial for loader sync.
                            window.parent.postMessage({ type: 'PREVIEW_UPDATE_COMPLETE' }, '*');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating preview:', error);
                        window.parent.postMessage({ type: 'PREVIEW_UPDATE_COMPLETE' }, '*');
                    });
                }
            });
        </script>
    @endif

    {!! captcha_render_script() !!}
</body>
</html>
