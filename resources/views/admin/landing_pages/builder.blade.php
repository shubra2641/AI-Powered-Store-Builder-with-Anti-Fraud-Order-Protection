@extends('layouts.admin')

@push('styles')

    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <style>
        #gjs { border: 3px solid #1a1a1a; border-radius: 15px; overflow: hidden; }
        .gjs-cv-canvas { background-color: #0d0d0d; }
    </style>
@endpush

@section('content')

<div class="d-flex justify-between align-center mb-5 p-4">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.page_builder') }}: {{ $landingPage->translations->first()?->title }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.customize_your_landing_page_with_ai') }}</p>
    </div>
    <div class="d-flex gap-3">
        <button id="builder-back-btn" class="btn-action d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700">
            <i class="fas fa-arrow-left fs-xs"></i>
            <span>{{ __('admin.back') }}</span>
        </button>
        <button id="save-builder-btn" class="btn-gradient d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700">
            <i class="fas fa-save fs-xs"></i>
            <span>{{ __('admin.save_changes') }}</span>
        </button>
    </div>
</div>

<div class="px-4 pb-4">
    <div class="d-flex gap-4 h-85vh">

        <div class="glass-card p-3 w-280 overflow-y-auto">
            <h5 class="fs-xs font-800 text-white mb-4 border-bottom border-white-5 pb-2 uppercase tracking-widest">
                <i class="fas fa-th-large me-2 text-primary"></i> {{ __('admin.blocks') }}
            </h5>
            <div id="blocks"></div>
        </div>


        <div class="flex-1 table-container p-0 overflow-hidden relative border-radius-lg border border-white-5">
            <div id="gjs"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/grapesjs"></script>
    <script>
        const editor = grapesjs.init({
            container: '#gjs',
            fromElement: false,
            storageManager: false,
            blockManager: {
                appendTo: '#blocks',
                blocks: [
                    @foreach($components as $comp)
                    {
                        id: 'section-{{ $comp->id }}',
                        label: '{{ $comp->name }}',
                        category: '{{ ucfirst($comp->category) }}',
                        content: {!! json_encode(view($comp->blade_template, ['section' => ['content' => $comp->config_schema, 'attributes' => []]])->render()) !!},
                        media: '<i class="fas fa-layer-group"></i>',
                    },
                    @endforeach
                ]
            },
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
                ]
            }
        });


        const bm = editor.BlockManager;
        editor.on('load', () => {
            const blocks = document.querySelector('#blocks');
            blocks.querySelectorAll('.gjs-block').forEach(el => {
                el.classList.add('glass-card', 'mb-2', 'p-3', 'text-white', 'text-center', 'cursor-pointer', 'hover:bg-primary-soft/20', 'transition-all', 'border', 'border-white-5', 'w-full', 'block');
            });
        });


        const builderContent = @json($landingPage->builder_content);
        if (builderContent && builderContent.length > 0) {
            // If it's section-based, we might need a different loader
            editor.setComponents(builderContent);
        }

        document.getElementById('builder-back-btn').onclick = () => window.history.back();

        document.getElementById('save-builder-btn').onclick = function() {
            DS_UI.loading(true);
            const components = editor.getComponents();
            const html = editor.getHtml();
            const css = editor.getCss();
            
            fetch("{{ route('admin.landing-pages.save', $landingPage->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    builder_content: components,
                    html: html,
                    css: css
                })
            })
            .then(res => res.json())
            .then(data => {
                DS_UI.loading(false);
                if (data.success) {
                    DS_UI.showToast('success', data.message);
                } else {
                    DS_UI.showToast('error', data.message || 'Save failed');
                }
            })
            .catch(err => {
                DS_UI.loading(false);
                DS_UI.showToast('error', 'Connection error');
            });
        };
    </script>
@endpush
