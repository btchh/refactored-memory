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
    $colorConfig = [
        'primary' => [
            'active' => 'bg-primary-600 text-white shadow-md shadow-primary-500/30 ring-2 ring-primary-600 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border border-gray-200 hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700',
            'dot' => 'bg-primary-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-primary-100 group-hover:text-primary-600',
        ],
        'yellow' => [
            'active' => 'bg-amber-500 text-white shadow-md shadow-amber-500/30 ring-2 ring-amber-500 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border border-gray-200 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700',
            'dot' => 'bg-amber-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-amber-100 group-hover:text-amber-600',
        ],
        'blue' => [
            'active' => 'bg-blue-500 text-white shadow-md shadow-blue-500/30 ring-2 ring-blue-500 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border border-gray-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700',
            'dot' => 'bg-blue-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600',
        ],
        'green' => [
            'active' => 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30 ring-2 ring-emerald-500 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700',
            'dot' => 'bg-emerald-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600',
        ],
        'red' => [
            'active' => 'bg-rose-500 text-white shadow-md shadow-rose-500/30 ring-2 ring-rose-500 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border border-gray-200 hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700',
            'dot' => 'bg-rose-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-rose-100 group-hover:text-rose-600',
        ],
        'purple' => [
            'active' => 'bg-violet-500 text-white shadow-md shadow-violet-500/30 ring-2 ring-violet-500 ring-offset-1',
            'inactive' => 'bg-white text-gray-600 border border-gray-200 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700',
            'dot' => 'bg-violet-500',
            'badge_active' => 'bg-white/25 text-white',
            'badge_inactive' => 'bg-gray-100 text-gray-500 group-hover:bg-violet-100 group-hover:text-violet-600',
        ],
    ];
@endphp

<div class="bg-white rounded-2xl border border-gray-200 p-6 print:hidden">
    {{-- Search Bar --}}
    @if($showSearch)
        <div class="relative mb-6">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" 
                   @if($searchId) id="{{ $searchId }}" @endif
                   name="{{ $searchName }}" 
                   value="{{ $searchValue ?? request($searchName) }}" 
                   placeholder="{{ $searchPlaceholder }}" 
                   class="w-full pl-12 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white transition-all"
                   @if(!$clientSide) form="filter-form" @endif>
            @if($clientSide)
                <button type="button" id="clear-filters" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
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
                   class="group px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ $isActive ? 'bg-primary-600 text-white shadow-md shadow-primary-500/30 ring-2 ring-primary-600 ring-offset-1' : 'bg-white text-gray-600 border border-gray-200 hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700' }}">
                    <span class="flex items-center gap-2">
                        @if($isActive)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                        <span>{{ $filter['label'] }}</span>
                        @if(isset($filter['count']))
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $isActive ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500 group-hover:bg-primary-100 group-hover:text-primary-600' }} transition-colors">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $startDateLabel }}</label>
                    <input type="date" 
                           name="{{ $startDateName }}" 
                           value="{{ $startDateValue ?? request($startDateName) }}" 
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white transition-all">
                </div>
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $endDateLabel }}</label>
                    <input type="date" 
                           name="{{ $endDateName }}" 
                           value="{{ $endDateValue ?? request($endDateName) }}" 
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white transition-all">
                </div>
            @endif

            {{-- Actions (only show if we have date inputs or fields) --}}
            @if($shouldShowDateInputs || isset($fields))
            <div class="form-group flex items-end gap-3">
                <button type="submit" class="flex-1 px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ $submitText }}
                </button>
                @if($showClear && $clearUrl)
                    <a href="{{ $clearUrl }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium rounded-xl transition-all flex items-center justify-center" title="Clear filters">
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
