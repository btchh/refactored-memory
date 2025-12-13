<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FilterService
{
    /**
     * Apply filters to a query builder
     */
    public function applyFilters(Builder $query, Request $request, array $config = []): Builder
    {
        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search filter
        if ($request->filled('search')) {
            $this->applySearch($query, $request->search, $config['searchFields'] ?? []);
        }

        // Date range filter
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $this->applyDateRange($query, $request, $config['dateField'] ?? 'created_at');
        }

        // Period filter (today, week, month, etc.)
        if ($request->filled('period') && $request->period !== 'all') {
            $this->applyPeriodFilter($query, $request->period, $config['dateField'] ?? 'created_at');
        }

        // Branch filter (for admin users)
        if ($request->filled('branch_id')) {
            $query->where('admin_id', $request->branch_id);
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Item type filter
        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        // Custom filters
        if (isset($config['customFilters'])) {
            foreach ($config['customFilters'] as $filter) {
                if ($request->filled($filter['field'])) {
                    $this->applyCustomFilter($query, $filter, $request->get($filter['field']));
                }
            }
        }

        return $query;
    }

    /**
     * Apply search across multiple fields
     */
    protected function applySearch(Builder $query, string $search, array $fields): void
    {
        if (empty($fields)) {
            return;
        }

        $query->where(function ($q) use ($search, $fields) {
            foreach ($fields as $field) {
                if (str_contains($field, '.')) {
                    // Relationship field
                    [$relation, $column] = explode('.', $field, 2);
                    $q->orWhereHas($relation, function ($subQuery) use ($column, $search) {
                        $subQuery->where($column, 'like', "%{$search}%");
                    });
                } else {
                    // Direct field
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            }
        });
    }

    /**
     * Apply date range filter
     */
    protected function applyDateRange(Builder $query, Request $request, string $dateField): void
    {
        if ($request->filled('start_date')) {
            $query->whereDate($dateField, '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate($dateField, '<=', $request->end_date);
        }
    }

    /**
     * Apply period-based filter (today, week, month, etc.)
     */
    protected function applyPeriodFilter(Builder $query, string $period, string $dateField): void
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $query->whereDate($dateField, $now->toDateString());
                break;

            case 'tomorrow':
                $query->whereDate($dateField, $now->addDay()->toDateString());
                break;

            case 'week':
                $query->whereBetween($dateField, [
                    $now->startOfWeek()->toDateString(),
                    $now->endOfWeek()->toDateString()
                ]);
                break;

            case 'month':
                $query->whereBetween($dateField, [
                    $now->startOfMonth()->toDateString(),
                    $now->endOfMonth()->toDateString()
                ]);
                break;

            case 'year':
                $query->whereBetween($dateField, [
                    $now->startOfYear()->toDateString(),
                    $now->endOfYear()->toDateString()
                ]);
                break;

            case 'last_week':
                $lastWeek = $now->subWeek();
                $query->whereBetween($dateField, [
                    $lastWeek->startOfWeek()->toDateString(),
                    $lastWeek->endOfWeek()->toDateString()
                ]);
                break;

            case 'last_month':
                $lastMonth = $now->subMonth();
                $query->whereBetween($dateField, [
                    $lastMonth->startOfMonth()->toDateString(),
                    $lastMonth->endOfMonth()->toDateString()
                ]);
                break;
        }
    }

    /**
     * Apply custom filter
     */
    protected function applyCustomFilter(Builder $query, array $filter, $value): void
    {
        $field = $filter['field'];
        $operator = $filter['operator'] ?? '=';
        $type = $filter['type'] ?? 'string';

        switch ($type) {
            case 'boolean':
                $query->where($field, (bool) $value);
                break;

            case 'numeric':
                $query->where($field, $operator, (float) $value);
                break;

            case 'array':
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                }
                break;

            case 'relation':
                if (isset($filter['relation'])) {
                    $query->whereHas($filter['relation'], function ($q) use ($filter, $value) {
                        $q->where($filter['relationField'], $filter['operator'] ?? '=', $value);
                    });
                }
                break;

            default:
                $query->where($field, $operator, $value);
                break;
        }
    }

    /**
     * Get filter statistics for status counts
     */
    public function getFilterStats(Builder $baseQuery, array $statusField = ['status']): array
    {
        $stats = [];
        
        // Clone the base query to avoid modifying it and remove ordering
        $query = clone $baseQuery;
        $query->getQuery()->orders = null; // Remove any ORDER BY clauses
        
        // Get total count
        $stats['all'] = $query->count();
        
        // Get counts by status
        if (count($statusField) === 1) {
            // Initialize all expected booking statuses with 0
            $expectedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
            foreach ($expectedStatuses as $status) {
                $stats[$status] = 0;
            }
            
            // Clone again and remove ordering for the GROUP BY query
            $statusQuery = clone $baseQuery;
            $statusQuery->getQuery()->orders = null; // Remove ORDER BY for GROUP BY compatibility
            
            $statusCounts = $statusQuery->selectRaw("{$statusField[0]}, COUNT(*) as count")
                ->groupBy($statusField[0])
                ->pluck('count', $statusField[0])
                ->toArray();
                
            // Merge actual counts, overriding the 0 defaults
            $stats = array_merge($stats, $statusCounts);
        }
        
        return $stats;
    }

    /**
     * Parse date filters from request
     */
    public function parseDateFilters(Request $request): array
    {
        $filters = [
            'start_date' => null,
            'end_date' => null,
            'period' => $request->get('period', 'all'),
            'is_custom' => false
        ];

        // Check for custom date range
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $filters['start_date'] = $request->start_date;
            $filters['end_date'] = $request->end_date;
            $filters['is_custom'] = true;
            $filters['period'] = 'custom';
        }
        // Check for period-based filters
        elseif ($request->filled('period') && $request->period !== 'all') {
            $dates = $this->getPeriodDates($request->period);
            $filters['start_date'] = $dates['start'];
            $filters['end_date'] = $dates['end'];
        }

        return $filters;
    }

    /**
     * Get start and end dates for a period
     */
    protected function getPeriodDates(string $period): array
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                return [
                    'start' => $now->toDateString(),
                    'end' => $now->toDateString()
                ];

            case 'tomorrow':
                $tomorrow = $now->addDay();
                return [
                    'start' => $tomorrow->toDateString(),
                    'end' => $tomorrow->toDateString()
                ];

            case 'week':
                return [
                    'start' => $now->startOfWeek()->toDateString(),
                    'end' => $now->endOfWeek()->toDateString()
                ];

            case 'month':
                return [
                    'start' => $now->startOfMonth()->toDateString(),
                    'end' => $now->endOfMonth()->toDateString()
                ];

            case 'year':
                return [
                    'start' => $now->startOfYear()->toDateString(),
                    'end' => $now->endOfYear()->toDateString()
                ];

            default:
                return ['start' => null, 'end' => null];
        }
    }

    /**
     * Build filter configuration for bookings
     */
    public static function bookingConfig(): array
    {
        return [
            'searchFields' => [
                'user.fname',
                'user.lname', 
                'user.email',
                'user.phone',
                'pickup_address',
                'notes'
            ],
            'dateField' => 'booking_date',
            'customFilters' => [
                [
                    'field' => 'is_upcoming',
                    'type' => 'boolean',
                    'operator' => '='
                ],
                [
                    'field' => 'total_price',
                    'type' => 'numeric',
                    'operator' => '>='
                ]
            ]
        ];
    }

    /**
     * Build filter configuration for users
     */
    public static function userConfig(): array
    {
        return [
            'searchFields' => [
                'fname',
                'lname',
                'email',
                'phone',
                'address'
            ],
            'dateField' => 'created_at'
        ];
    }

    /**
     * Build filter configuration for user history
     */
    public static function userHistoryConfig(): array
    {
        return [
            'searchFields' => [
                'pickup_address',
                'notes'
            ],
            'dateField' => 'booking_date',
            'customFilters' => [
                [
                    'field' => 'item_type',
                    'type' => 'string',
                    'operator' => '='
                ]
            ]
        ];
    }

    /**
     * Build filter configuration for admin management
     */
    public static function adminManagementConfig(): array
    {
        return [
            'searchFields' => [
                'user.fname',
                'user.lname', 
                'user.email',
                'pickup_address'
            ],
            'dateField' => 'booking_date',
            'customFilters' => [
                [
                    'field' => 'item_type',
                    'type' => 'string',
                    'operator' => '='
                ],
                [
                    'field' => 'admin_id',
                    'type' => 'numeric',
                    'operator' => '='
                ]
            ]
        ];
    }
}