@extends('layouts.admin')

@section('content')

    <div class="grid cols-4 gap-4 mb-5">
        <div class="stat-card-premium">
            <div class="d-flex justify-between align-start">
                <div>
                    
                    <h2 class="fs-xl font-800 m-0">{{ $metrics['requests_today']['value'] }}</h2>
                    <p class="text-muted fs-xs mt-1">{{ __('auth.today_requests') }}</p>
                </div>
                <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-purple">
                    <i class="fas fa-bolt fs-lg"></i>
                </div>
            </div>
            <div class="stat-trend text-success mt-3 fs-xs font-600">
                <i class="fas fa-arrow-up"></i> {{ $metrics['requests_today']['trend'] }}
            </div>
        </div>

        <div class="stat-card-premium">
            <div class="d-flex justify-between align-start">
                <div>
                    <h2 class="fs-xl font-800 m-0">{{ $metrics['pages_total']['value'] }}</h2>
                    <p class="text-muted fs-xs mt-1">@lang('admin.total_pages')</p>
                </div>
                <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-cyan">
                    <i class="far fa-clock fs-lg"></i>
                </div>
            </div>
            <div class="stat-trend text-danger mt-3 fs-xs font-600">
                <i class="fas fa-arrow-down"></i> {{ $metrics['pages_total']['trend'] }}
            </div>
        </div>

        <div class="stat-card-premium">
            <div class="d-flex justify-between align-start">
                <div>
                    <h2 class="fs-xl font-800 m-0">{{ $metrics['monthly_cost']['value'] }}</h2>
                    <p class="text-muted fs-xs mt-1">{{ __('auth.monthly_cost') }}</p>
                </div>
                <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-orange">
                    <i class="fas fa-coins fs-lg"></i>
                </div>
            </div>
            <div class="stat-trend text-success mt-3 fs-xs font-600">
                <i class="fas fa-arrow-up"></i> {{ $metrics['monthly_cost']['trend'] }}
            </div>
        </div>

        <div class="stat-card-premium">
            <div class="d-flex justify-between align-start">
                <div>
                    <h2 class="fs-xl font-800 m-0">{{ $metrics['active_users']['value'] }}</h2>
                    <p class="text-muted fs-xs mt-1">{{ __('auth.active_users') }}</p>
                </div>
                <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-pink">
                    <i class="fas fa-users fs-lg"></i>
                </div>
            </div>
            <div class="stat-trend text-success mt-3 fs-xs font-600">
                <i class="fas fa-arrow-up"></i> {{ $metrics['active_users']['trend'] }}
            </div>
        </div>
    </div>




    <div class="grid cols-2 gap-6 mb-8">
        <!-- Usage Chart (Multi-Wave) -->
        <div class="glass-card p-6 position-relative overflow-hidden">
            <div class="wave-bg"></div>
            <div class="d-flex align-center justify-between mb-6 position-relative z-10">
                <h3 class="fs-md font-800 m-0">@lang('auth.service_usage')</h3>
                <div class="d-flex align-center gap-2" id="chart-legends">
                    @foreach($metrics['wave_data']['datasets'] as $index => $dataset)
                    <button class="badge-tag border-none cursor-pointer transition-all chart-legend-item" 
                            data-index="{{ $index }}"
                            data-fill="{{ $dataset['fill_color'] }}"
                            data-color="{{ $dataset['color'] }}">
                        <i class="fas fa-circle me-1 fs-3xs"></i>
                        {{ $dataset['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>
            
            <div class="position-relative z-10 h-220">
                <canvas id="usageWaveChart"></canvas>
            </div>
        </div>

        <!-- AI Providers Status -->
        <div class="glass-card p-6 position-relative overflow-hidden">
            <div class="wave-bg"></div>
            <div class="d-flex align-center justify-between mb-6 position-relative z-10">
                <h3 class="fs-md font-800 m-0">@lang('admin.models_status')</h3>
                <span class="fs-2xs text-success d-flex align-center gap-2">
                    <div class="w-8 h-8 border-radius-full bg-success status-dot"></div>
                    @lang('admin.all_models_operational')
                </span>
            </div>
            <div class="vstack gap-4 position-relative z-10">
                @forelse($metrics['provider_status'] as $provider)
                <div class="d-flex align-center gap-4">
                    <div class="w-40 h-40 border-radius-sm bg-{{ $provider['color'] }}-soft-overlay d-flex align-center justify-center">
                        <i class="fas {{ $provider['icon'] }} text-{{ $provider['color'] }}"></i>
                    </div>
                    <div class="flex-1">
                        <div class="d-flex align-center justify-between mb-1">
                            <span class="fs-sm font-600">{{ $provider['name'] }}</span>
                            <span class="text-muted fs-xs mb-3">{{ $provider['count'] }} API Keys</span>
                        </div>
                        <div class="progress-container progress-bg-opacity m-0 progress-h-6">
                            <div class="progress-bar progress-gradient-{{ $provider['color'] }} w-{{ (int)$provider['percentage'] }}"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <p class="text-muted fs-xs">@lang('admin.no_active_keys')</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>


    <div class="grid cols-2 gap-6 mt-6">
        <!-- Recent Activity & Transactions -->
        <div class="glass-card p-6 position-relative overflow-hidden h-full">
            <div class="wave-bg opacity-10"></div>
            <div class="d-flex align-center justify-between mb-6 position-relative z-10">
                <div>
                    <h3 class="fs-md font-800 m-0">@lang('admin.recent_activity')</h3>
                    <p class="text-muted fs-xs m-0">@lang('admin.monitor_latest_transactions')</p>
                </div>
                <a href="{{ route('admin.transactions.index') }}" class="fs-2xs text-success d-flex align-center gap-2">
                    @lang('admin.view_all') <i class="fas fa-arrow-right fs-xs ms-1"></i>
                </a>
            </div>

            <div id="transactions-wrapper" class="position-relative z-10">
                @include('admin.dashboard.partials._transactions_table')
            </div>
        </div>

        <!-- Latest Subscribers -->
        <div class="glass-card p-6 position-relative overflow-hidden h-full">
            <div class="wave-bg opacity-10"></div>
            <div class="d-flex align-center justify-between mb-6 position-relative z-10">
                <div>
                    <h2 class="fs-md font-800 m-0">@lang('admin.latest_subscribers')</h2>
                    <p class="text-muted fs-xs m-0">@lang('admin.subscription_stats')</p>
                </div>
                <a href="{{ route('admin.plans.index') }}" class="fs-2xs text-success d-flex align-center gap-2">
                    @lang('admin.view_all') <i class="fas fa-arrow-right fs-xs ms-1"></i>
                </a>
            </div>

            <div id="subscribers-wrapper" class="position-relative z-10">
                @include('admin.dashboard.partials._subscribers_table')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const handleAjaxPagination = (wrapperId) => {
            const wrapper = document.getElementById(wrapperId);
            if (!wrapper) return;

            wrapper.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination-premium a');
                if (link) {
                    e.preventDefault();
                    const url = link.getAttribute('href');
                    
                    wrapper.classList.add('opacity-50', 'pointer-none');

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        wrapper.innerHTML = html;
                        wrapper.classList.remove('opacity-50', 'pointer-none');
                    })
                    .catch(error => {
                        console.error(`Error fetching ${wrapperId}:`, error);
                        wrapper.classList.remove('opacity-50', 'pointer-none');
                    });
                }
            });
        };

        handleAjaxPagination('transactions-wrapper');
        handleAjaxPagination('subscribers-wrapper');
    });
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let usageChart;

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('usageWaveChart').getContext('2d');
        const waveData = @json($metrics['wave_data']);

        const datasets = waveData.datasets.map(ds => ({
            label: ds.label,
            data: ds.data,
            borderColor: ds.color,
            backgroundColor: ds.fill_color,
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHoverBackgroundColor: ds.color,
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 2
        }));

        usageChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: waveData.labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // We use custom legends
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.4)',
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.4)',
                            font: { size: 10 }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                }
            }
        });

        // Typing Effect
        const typingElement = document.getElementById('typing-text');
        if (typingElement) {
            const texts = ["@lang('admin.discover_ai_power')", "@lang('admin.create_pro_content')", "@lang('admin.analyze_data_smartly')", "@lang('admin.program_efficiently')"];
            let tIdx = 0, cIdx = 0, isDel = false;
            function type() {
                const curr = texts[tIdx] || "";
                typingElement.textContent = isDel ? curr.substring(0, cIdx - 1) : curr.substring(0, cIdx + 1);
                isDel ? cIdx-- : cIdx++;
                let spd = isDel ? 30 : 50;
                if (!isDel && cIdx === curr.length) { spd = 2000; isDel = true; }
                else if (isDel && cIdx === 0) { isDel = false; tIdx = (tIdx + 1) % texts.length; spd = 500; }
                setTimeout(type, spd);
            }
            type();
        }
        // Chart Legends Initialization & Event
        document.querySelectorAll('.chart-legend-item').forEach(btn => {
            const fill = btn.dataset.fill;
            const color = btn.dataset.color;
            btn.style.setProperty('--legend-bg', fill);
            btn.style.setProperty('--legend-color', color);
            btn.style.backgroundColor = fill;
            btn.style.color = color;
            btn.style.borderColor = color + '44';

            btn.addEventListener('click', function() {
                toggleDataset(parseInt(this.dataset.index), this);
            });
        });

    });

    function toggleDataset(index, element) {
        const isHidden = usageChart.isDatasetVisible(index);
        
        if (isHidden) {
            usageChart.hide(index);
            element.classList.add('opacity-40', 'grayscale');
        } else {
            usageChart.show(index);
            element.classList.remove('opacity-40', 'grayscale');
        }
    }
</script>
@endpush
