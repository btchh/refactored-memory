{{--
    Filter Panel Component
    A reusable filter/search UI component for consistent filtering across admin and user interfaces.
    
    Status Filters Usage (colored tabs with counts):
    <x-modules.filter-panel
        :status-filters="[
            ['key' => 'all', 'label' => 'All', 'count' => 10, 'color' => 'primary', 'icon' => 'list'],
            ['key' => 'pending', 'label' => 'Pending', 'count' => 3, 'color' => 'yellow'],
            ['key' => 'completed', 'label' => 'Completed', 'count' => 5, 'color' => 'green'],
        ]"
        :current-status="request('status', 'all')"
        :show-search="true"
        search-id="search-input"
        :client-side="true"
    />
--}}

@props([
    'action' => null,
    'method' => 'GET',
    'quickFilters' => [],
    'statusFilters' => [],
    'currentStatus' => 'all',
    'showSearch' => false,
    'searchPlaceholder' => 'Search...',
    'searchName' => 'search',
    'searchValue' => null,
    'searchId' => null,
    'showDateRange' => false,
    'showCustomDateFilter' => false,
    'startDateName' => 'start_date',
    'endDateName' => 'end_date',
    'startDateValue' => null,
    'endDateValue' => null,
    'startDateLabel' => 'Start Date',
    'endDateLabel' => 'End Date',
    'clearUrl' => null,
    'showClear' => true,
    'submitText' => 'Apply Filters',
    'gridCols' => 'lg:grid-cols-4',
    'clientSide' => false,
])

@php
    // Determine if we should show the date range inputs
    $isCustomDateActive = $showCustomDateFilter && $currentStatus === 'custom';
    $shouldShowDateInputs = $showDateRange && (!$showCustomDateFilter || $isCustomDateActive);
@endphp

@php
    // WashHour Design System - Color configuration with wash accent
    $colorConfig = [
        'primary' => [
            'active' => 'bg-wash text-white shadow-md shadow-wash/30 ring-2 ring-wash ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border-2 border-gray-200 hover:border-wash hover:bg-wash/5 hover:text-wash',
            'dot' => 'bg-wash',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-wash/10 group-hover:text-wash',
        ],
        'yellow' => [
            'active' => 'bg-warning text-white shadow-md shadow-warning/30 ring-2 ring-warning ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border-2 border-gray-200 hover:border-warning hover:bg-warning/5 hover:text-warning',
            'dot' => 'bg-warning',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-warning/10 group-hover:text-warning',
        ],
        'blue' => [
            'active' => 'bg-info text-white shadow-md shadow-info/30 ring-2 ring-info ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border-2 border-gray-200 hover:border-info hover:bg-info/5 hover:text-info',
            'dot' => 'bg-info',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-info/10 group-hover:text-info',
        ],
        'green' => [
            'active' => 'bg-success text-white shadow-md shadow-success/30 ring-2 ring-success ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border-2 border-gray-200 hover:border-success hover:bg-success/5 hover:text-success',
            'dot' => 'bg-success',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-success/10 group-hover:text-success',
        ],
        'gray' => [
            'active' => 'bg-gray-500 text-white shadow-md shadow-gray-500/30 ring-2 ring-gray-500 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700',
            'dot' => 'bg-gray-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-gray-200 group-hover:text-gray-700',
        ],
        'red' => [
            'active' => 'bg-error text-white shadow-md shadow-error/30 ring-2 ring-error ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border-2 border-gray-200 hover:border-error hover:bg-error/5 hover:text-error',
            'dot' => 'bg-error',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-error/10 group-hover:text-error',
        ],
        'purple' => [
            'active' => 'bg-violet-500 text-white shadow-md shadow-violet-500/30 ring-2 ring-violet-500 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border-2 border-gray-200 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700',
            'dot' => 'bg-violet-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-violet-100 group-hover:text-violet-600',
        ],
    ];
@endphp

<div class="bg-white rounded-2xl border-2 border-gray-200 p-6 print:hidden">
    {{-- Search Bar with form-input styling --}}
    @if($showSearch)
        <div class="form-group mb-6">
            <div class="relative">
                <input type="text" 
                       @if($searchId) id="{{ $searchId }}" @endif
                       name="{{ $searchName }}" 
                       value="{{ $searchValue ?? request($searchName) }}" 
                       placeholder="{{ $searchPlaceholder }}" 
                       class="form-input pr-12"
                       @if(!$clientSide) form="filter-form" @endif>
                @if($clientSide)
                    <button type="button" id="clear-filters" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Status Filter Tabs (colored badges with counts) --}}
    @if(count($statusFilters) > 0)
        <div class="flex flex-wrap gap-2.5{{ (count($quickFilters) > 0 || $shouldShowDateInputs || isset($fields)) ? ' mb-6' : '' }}">
            @foreach($statusFilters as $filter)
                @php
                    $color = $filter['color'] ?? 'primary';
                    $config = $colorConfig[$color] ?? $colorConfig['primary'];
                    $isActive = ($filter['key'] ?? '') === $currentStatus;
                    $hasIcon = isset($filter['icon']) && $filter['icon'] === 'list';
                @endphp
                <button type="button"
                        data-filter="{{ $filter['key'] ?? '' }}"
                        class="filter-btn group relative px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ $isActive ? $config['active'] : $config['inactive'] }}">
                    <span class="flex items-center gap-2">
                        @if($hasIcon)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        @else
                            <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-white' : $config['dot'] }} transition-colors"></span>
                        @endif
                        <span>{{ $filter['label'] }}</span>
                        @if(isset($filter['count']))
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $isActive ? $config['badge_active'] : $config['badge_inactive'] }} transition-colors">
                                {{ $filter['count'] }}
                            </span>
                        @endif
                    </span>
                </button>
            @endforeach
        </div>
    @endif

    {{-- Quick Filters (link-based) --}}
    @if(count($quickFilters) > 0)
        <div class="flex flex-wrap gap-2.5 mb-6">
            @foreach($quickFilters as $filter)
                @php
                    $isActive = $filter['active'] ?? false;
                @endphp
                <a href="{{ $filter['url'] }}" 
                   class="group px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ $isActive ? 'bg-wash text-white shadow-md shadow-wash/30 ring-2 ring-wash ring-offset-1' : 'bg-white text-gray-600 border-2 border-gray-200 hover:border-wash hover:bg-wash/5 hover:text-wash' }}">
                    <span class="flex items-center gap-2">
                        @if($isActive)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                        <span>{{ $filter['label'] }}</span>
                        @if(isset($filter['count']))
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $isActive ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500 group-hover:bg-wash/10 group-hover:text-wash' }} transition-colors">
                                {{ $filter['count'] }}
                            </span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    @endif

    {{-- Filter Form --}}
    <form id="filter-form" method="{{ $method }}" @if($action) action="{{ $action }}" @endif>
        {{-- Hidden Fields Slot --}}
        {{ $hidden ?? '' }}

        @if($shouldShowDateInputs || isset($fields))
        <div id="date-range-section" class="grid grid-cols-1 md:grid-cols-2 {{ $gridCols }} gap-4">
            {{-- Custom Fields Slot --}}
            {{ $fields ?? '' }}

            {{-- Date Range (only shown when not using custom filter OR when custom is active) --}}
            @if($shouldShowDateInputs)
                <div class="form-group">
                    <label class="form-label">{{ $startDateLabel }}</label>
                    <input type="date" 
                           name="{{ $startDateName }}" 
                           value="{{ $startDateValue ?? request($startDateName) }}" 
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $endDateLabel }}</label>
                    <input type="date" 
                           name="{{ $endDateName }}" 
                           value="{{ $endDateValue ?? request($endDateName) }}" 
                           class="form-input">
                </div>
            @endif

            {{-- Actions (only show if we have date inputs or fields) --}}
            @if($shouldShowDateInputs || isset($fields))
            <div class="form-group flex items-end gap-3">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ $submitText }}
                </button>
                @if($showClear && $clearUrl)
                    <a href="{{ $clearUrl }}" class="btn btn-secondary" title="Clear filters">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
            @endif
        </div>
        @endif

        {{-- Additional Content Slot --}}
        {{ $after ?? '' }}
    </form>
</div>
