@extends('layouts.admin')

@section('content')

<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.plans') }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.manage_plans_desc') }}</p>
    </div>
    <button data-ds-modal-open="addPlanModal" class="btn-gradient d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700">
        <i class="fas fa-plus fs-xs"></i>
        <span>{{ __('admin.add_plan') }}</span>
    </button>
</div>


<div class="grid cols-4 gap-4 mb-5">
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ $stats['total_plans'] }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.total_plans') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-purple">
                <i class="fas fa-layer-group fs-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ number_format($stats['active_users']) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.active_subscriber') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-cyan">
                <i class="fas fa-users fs-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ ds_currency($stats['monthly_revenue']) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.month_revenue') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-orange">
                <i class="fas fa-wallet fs-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ $stats['conversion_rate'] }}%</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.conversion_rate') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-pink">
                <i class="fas fa-chart-pie fs-lg"></i>
            </div>
        </div>
    </div>
</div>


<div class="grid cols-3 gap-6 mb-8" id="plansContainer">
    @foreach($plans as $plan)
        @php
            $icons = [
                'free' => 'fa-gift',
                'starter' => 'fa-rocket',
                'pro' => 'fa-star',
                'business' => 'fa-briefcase',
                'enterprise' => 'fa-building',
            ];
            $slug = strtolower($plan->name['en'] ?? '');
            $cardIcon = 'fa-layer-group';
            foreach ($icons as $key => $icon) {
                if (str_contains($slug, $key)) {
                    $cardIcon = $icon;
                    break;
                }
            }

            $iconBgClass = $plan->is_featured ? 'bg-gradient-to-br from-primary to-secondary' : '';
            $iconColorClass = $plan->is_featured ? 'text-white' : '';

            if (!$plan->is_featured) {
                 $bgColors = [
                    'bg-gray-500/20',
                    'bg-blue-500/20',
                    'bg-accent/20',
                    'bg-pink-500/20',
                    'bg-purple-500/20'
                ];
                $iconBgClass = $bgColors[($plan->id ?? 0) % count($bgColors)];

                $textColors = [
                    'text-gray-400',
                    'text-blue-400',
                    'text-accent',
                    'text-pink-400',
                    'text-purple-400'
                ];
                $iconColorClass = $textColors[($plan->id ?? 0) % count($textColors)];
            }
        @endphp

        <div class="glass-card plan-card {{ $plan->is_featured ? 'featured' : '' }}">
            @if($plan->is_featured)
                <div class="featured-badge">{{ __('admin.most_popular') }}</div>
            @endif

            <div class="d-flex items-center justify-between mb-4 mt-2">
                <div class="avatar-56 {{ $iconBgClass }} d-flex items-center justify-center">
                    <i class="fas {{ $cardIcon }} {{ $iconColorClass }} text-2xl"></i>
                </div>
                <div class="d-flex align-center gap-2">
                    @if($plan->is_default)
                        <span class="badge-tag badge-tag-success fs-3xs py-1 px-2">
                            <i class="fas fa-star me-1 text-yellow"></i> {{ __('admin.default') }}
                        </span>
                    @else
                        <button type="button" class="btn-action btn-action-view h-24 fs-3xs py-0 px-2 set-default-btn badge-tag badge-tag-neutral border-0 cursor-pointer hover-scale" 
                                data-plan-id="{{ $plan->id }}" 
                                data-route="{{ route('admin.plans.set-default', $plan) }}">
                            <i class="far fa-star me-1 text-muted"></i> {{ __('admin.make_default') }}
                        </button>
                    @endif
                    <label class="ds-switch">
                        <input type="checkbox" {{ $plan->is_active ? 'checked' : '' }} 
                               class="plan-status-toggle" 
                               data-plan-id="{{ $plan->id }}"
                               data-route="{{ route('admin.plans.toggle', $plan) }}">
                        <span class="ds-switch-slider"></span>
                    </label>
                </div>
            </div>

            <h3 class="fs-xl font-bold mb-2">{{ $plan->translated_name }}</h3>
            <p class="text-muted fs-sm mb-4">{{ str($plan->translated_description)->limit(60) }}</p>

            <div class="mb-6">
                <span class="fs-3xl font-bold {{ $plan->is_featured ? 'gradient-text' : 'text-white' }}">{{ ds_currency($plan->price) }}</span>
                <span class="text-muted">{{ __('admin.per_month') }}</span>
            </div>

            <div class="vstack gap-3 mb-6">
                @foreach($plan->quotas as $key => $value)
                    <div class="feature-item {{ $value == 0 ? 'disabled' : '' }}">
                        @if($value != 0)
                            <i class="fas fa-check text-emerald fs-xs"></i>
                        @else
                            <i class="fas fa-times text-danger fs-xs"></i>
                        @endif
                        <span class="fs-sm">
                            @if(is_array($value))
                                {{ count($value) }} {{ __('admin.'.$key) }}
                            @else
                                {{ $value == -1 ? __('admin.unlimited') : number_format($value) }} {{ __('admin.'.$key) }}
                            @endif
                        </span>
                    </div>
                @endforeach
                

                <div class="feature-item">
                    <i class="fas fa-check text-emerald fs-xs"></i>
                    <span class="fs-sm">API Access</span>
                </div>
            </div>

            <div class="d-flex items-center gap-2 mt-auto">
                <button type="button" class="btn-action btn-action-edit flex-1 rounded-lg d-flex align-center justify-center gap-2 py-2 edit-plan-btn h-40" 
                        data-plan="{{ $plan->toJson() }}">
                    <i class="fas fa-edit"></i>
                    <span class="font-800">{{ __('admin.edit') }}</span>
                </button>
                <button type="button" class="btn-action btn-action-delete rounded-lg d-flex align-center justify-center w-40 h-40" 
                        data-ds-confirm="{{ route('admin.plans.destroy', $plan->id) }}"
                        data-ds-message="{{ __('admin.confirm_delete') }}"
                        data-ds-method="DELETE"
                        data-ds-btn-class="bg-danger"
                        title="{{ __('admin.delete') }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    @endforeach
</div>


<div class="glass-card">
    <div class="d-flex align-center justify-between mb-6">
        <h3 class="fs-lg font-bold text-white m-0">{{ __('admin.latest_subscribers') }}</h3>
        <a href="{{ route('admin.transactions.index') }}" class="text-sm text-primary hover-underline decoration-none">{{ __('admin.view_all') }}</a>
    </div>
    
    <div class="table-container table-responsive-premium">
        <table class="table-premium">
            <thead>
                <tr>
                    <th class="text-right">{{ __('admin.user') }}</th>
                    <th class="text-right">{{ __('admin.plan') }}</th>
                    <th class="text-right d-none-mobile">{{ __('admin.start_date') }}</th>
                    <th class="text-right d-none-mobile">{{ __('admin.end_date') }}</th>
                    <th class="text-center">{{ __('admin.status') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestSubscriptions as $sub)
                    <tr class="hover-bg-white-5 transition-all">
                        <td>
                            <div class="d-flex align-center gap-3">
                                <div class="avatar-40 rounded-lg bg-primary/20 d-flex align-center justify-center">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div class="vstack">
                                    <p class="font-700 m-0 fs-sm">{{ $sub->user->name }}</p>
                                    <p class="fs-2xs text-muted m-0">{{ $sub->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-tag badge-tag-purple fs-2xs font-700">{{ $sub->plan->translated_name }}</span>
                        </td>
                        <td class="d-none-mobile"><span class="fs-xs text-muted">{{ $sub->created_at->format('Y/m/d') }}</span></td>
                        <td class="d-none-mobile"><span class="fs-xs text-muted">{{ $sub->ends_at ? $sub->ends_at->format('Y/m/d') : '∞' }}</span></td>
                        <td class="text-center">
                            @if($sub->status === 'active')
                                <span class="status-badge status-active fs-3xs">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('admin.active') }}
                                </span>
                            @else
                                <span class="status-badge status-inactive fs-3xs">
                                    <i class="fas fa-clock me-1"></i> {{ __('admin.' . $sub->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button class="btn-action btn-action-edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('admin.plans.modals')

@endsection

@push('scripts')
<script src="{{ asset('assets/js/admin-plans.js') }}"></script>
@endpush
