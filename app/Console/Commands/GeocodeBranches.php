<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeocodeService;

class GeocodeBranches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'branches:geocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode all branch addresses to get coordinates';

    /**
     * Execute the console command.
     */
    public function handle(GeocodeService $geocodeService)
    {
        $this->info('Starting branch geocoding...');
        
        $results = $geocodeService->geocodeAllBranches();
        
        $this->info("Geocoding complete!");
        $this->info("Success: {$results['success']}");
        $this->info("Failed: {$results['failed']}");
        $this->info("Skipped: {$results['skipped']}");
        
        if (!empty($results['details'])) {
            $this->table(
                ['Branch', 'Status', 'Coordinates/Reason'],
                collect($results['details'])->map(function($detail) {
                    return [
                        $detail['branch'],
                        $detail['status'],
                        $detail['status'] === 'success' 
                            ? "{$detail['coordinates']['latitude']}, {$detail['coordinates']['longitude']}"
                            : ($detail['reason'] ?? 'N/A')
                    ];
                })
            );
        }
        
        return 0;
    }
}
