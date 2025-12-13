<?php

namespace App\Http\Traits;

use App\Services\FilterService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    protected FilterService $filterService;

    /**
     * Initialize the filter service
     */
    protected function initializeFilters(): void
    {
        $this->filterService = app(FilterService::class);
    }

    /**
     * Apply filters to a query
     */
    protected function applyFilters(Builder $query, Request $request, array $config = []): Builder
    {
        if (!isset($this->filterService)) {
            $this->initializeFilters();
        }

        return $this->filterService->applyFilters($query, $request, $config);
    }

    /**
     * Get filter statistics
     */
    protected function getFilterStats(Builder $query, array $statusField = ['status']): array
    {
        if (!isset($this->filterService)) {
            $this->initializeFilters();
        }

        return $this->filterService->getFilterStats($query, $statusField);
    }

    /**
     * Parse date filters from request
     */
    protected function parseDateFilters(Request $request): array
    {
        if (!isset($this->filterService)) {
            $this->initializeFilters();
        }

        return $this->filterService->parseDateFilters($request);
    }

    /**
     * Get paginated and filtered results
     */
    protected function getPaginatedResults(Builder $query, Request $request, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Build filter response data
     */
    protected function buildFilterResponse(Request $request, $results, array $stats = [], array $additionalData = []): array
    {
        $dateFilters = $this->parseDateFilters($request);
        
        return array_merge([
            'results' => $results,
            'stats' => $stats,
            'filters' => [
                'status' => $request->get('status', 'all'),
                'search' => $request->get('search', ''),
                'start_date' => $dateFilters['start_date'],
                'end_date' => $dateFilters['end_date'],
                'period' => $dateFilters['period'],
                'is_custom' => $dateFilters['is_custom']
            ]
        ], $additionalData);
    }
}