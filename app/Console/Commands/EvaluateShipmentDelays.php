<?php

namespace App\Console\Commands;

use App\Services\EtaService;
use Illuminate\Console\Command;

class EvaluateShipmentDelays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:evaluate-delays {--threshold= : Optional threshold override in hours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate all active shipments against delay threshold and update ETA status';

    /**
     * Execute the console command.
     */
    public function handle(EtaService $etaService): int
    {
        $threshold = $this->option('threshold') ? (int) $this->option('threshold') : null;
        $activeThreshold = $threshold ?? $etaService->getThresholdHours();

        $this->info("Evaluating active shipments against delay threshold: {$activeThreshold} hours...");

        $count = $etaService->evaluateBatch($threshold);

        $this->info("Successfully evaluated {$count} active shipments.");

        return Command::SUCCESS;
    }
}
