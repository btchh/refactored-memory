{{--
    Filter Panel Component
    A reusable filter/search UI component for consistent filtering across admin and user interfaces.
    
    Usage:
    <x-modules.filter-panel
        :action="route('your.route')"
        :quick-filters="[
            ['label' => 'Today', 'url' => route('...'), 'active' => true/false],
            ['label' => 'This Week', 'url' => route('...')],
        ]"
        :show-search="true"
        search-placeholder="Search..."
        :show-date-range="true"
        :clear-url="route('your.clear.route')"
    >
        <x-slot name="hidden">
            <input type="hidden" name="status" value="...">
        </x-slot>
        <x-slot name="fields">
            <!-- Custom filter fields -->
        </x-slot>
        <x-slot name="after">
            <!-- Content after the form grid -->
        </x-slot>
    </x-modules.filter-panel>
--}}

@props([
    'action' => null,
    'method' => 'GET',
    'quickFilters' => [],
    'showSearch' => false,
    'searchPlaceholder' => 'Search...',
    'searchName' => 'search',
    'searchValue' => null,
    'showDateRange' => false,
    'startDateName' => 'start_date',
    'endDateName' => 'end_date',
    'startDateValue' => null,
    'endDateValue' => null,
    'startDateLabel' => 'Start Date',
    'endDateLabel' => 'End Date',
    'clearUrl' => null,
    'showClear' => true,
    'submitText' => 'Filter',
    'gridCols' => 'lg:grid-cols-5',
])

<x-modules.card class="p-6 print:hidden">
    {{-- Quick Filters --}}
    @if(count($quickFilters) > 0)
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="text-xs font-medium text-gray-600">Quick:</span>
            @foreach($quickFilters as $filter)
                <a href="{{ $filter['url'] }}" 
                   class="px-2 py-1 text-xs rounded transition-colors {{ ($filter['active'] ?? false) ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                    {{ $filter['label'] }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="{{ $method }}" @if($action) action="{{ $action }}" @endif class="space-y-4">
        {{-- Hidden Fields Slot --}}
        {{ $hidden ?? '' }}

        <div class="grid grid-cols-1 md:grid-cols-2 {{ $gridCols }} gap-4">
            {{-- Search Field --}}
            @if($showSearch)
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="{{ $searchName }}" 
                           value="{{ $searchValue ?? request($searchName) }}" 
                           placeholder="{{ $searchPlaceholder }}" 
                           class="form-input w-full">
                </div>
            @endif

            {{-- Custom Fields Slot --}}
            {{ $fields ?? '' }}

            {{-- Date Range --}}
            @if($showDateRange)
                <div class="form-group">
                    <label class="form-label">{{ $startDateLabel }}</label>
                    <input type="date" 
                           name="{{ $startDateName }}" 
                           value="{{ $startDateValue ?? request($startDateName) }}" 
                           class="form-input w-full">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ $endDateLabel }}</label>
                    <input type="date" 
                           name="{{ $endDateName }}" 
                           value="{{ $endDateValue ?? request($endDateName) }}" 
                           class="form-input w-full">
                </div>
            @endif

            {{-- Actions --}}
            <div class="form-group flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">{{ $submitText }}</button>
                @if($showClear && $clearUrl)
                    <a href="{{ $clearUrl }}" class="btn btn-outline">Clear</a>
                @endif
            </div>
        </div>

        {{-- Additional Content Slot --}}
        {{ $after ?? '' }}
    </form>
</x-modules.card>
