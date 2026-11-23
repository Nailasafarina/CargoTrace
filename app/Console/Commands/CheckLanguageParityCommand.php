<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckLanguageParityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:check-parity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that all translation keys between lang/id and lang/en are 100% synchronized';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $idPath = base_path('lang/id');
        $enPath = base_path('lang/en');

        if (! is_dir($idPath) || ! is_dir($enPath)) {
            $this->error('Language directories lang/id and lang/en must exist.');
            return Command::FAILURE;
        }

        $idFiles = glob("{$idPath}/*.php") ?: [];
        $enFiles = glob("{$enPath}/*.php") ?: [];

        $idBasenames = array_map('basename', $idFiles);
        $enBasenames = array_map('basename', $enFiles);

        $missingInIdFiles = array_diff($enBasenames, $idBasenames);
        $missingInEnFiles = array_diff($idBasenames, $enBasenames);

        $hasError = false;

        if (! empty($missingInIdFiles)) {
            $this->error('Translation files missing in lang/id: '.implode(', ', $missingInIdFiles));
            $hasError = true;
        }

        if (! empty($missingInEnFiles)) {
            $this->error('Translation files missing in lang/en: '.implode(', ', $missingInEnFiles));
            $hasError = true;
        }

        $allBasenames = array_unique(array_merge($idBasenames, $enBasenames));

        $missingKeysInId = [];
        $missingKeysInEn = [];
        $totalKeysChecked = 0;

        foreach ($allBasenames as $file) {
            $group = pathinfo($file, PATHINFO_FILENAME);
            $idArray = file_exists("{$idPath}/{$file}") ? require "{$idPath}/{$file}" : [];
            $enArray = file_exists("{$enPath}/{$file}") ? require "{$enPath}/{$file}" : [];

            if (! is_array($idArray)) {
                $idArray = [];
            }
            if (! is_array($enArray)) {
                $enArray = [];
            }

            $idFlattened = $this->flattenKeys($idArray, $group);
            $enFlattened = $this->flattenKeys($enArray, $group);

            $totalKeysChecked += max(count($idFlattened), count($enFlattened));

            $diffId = array_diff($enFlattened, $idFlattened);
            $diffEn = array_diff($idFlattened, $enFlattened);

            if (! empty($diffId)) {
                $missingKeysInId = array_merge($missingKeysInId, $diffId);
            }
            if (! empty($diffEn)) {
                $missingKeysInEn = array_merge($missingKeysInEn, $diffEn);
            }
        }

        if (! empty($missingKeysInId)) {
            $this->error('The following translation keys exist in [en] but are MISSING in [id]:');
            foreach ($missingKeysInId as $k) {
                $this->line("  - <fg=red>{$k}</>");
            }
            $hasError = true;
        }

        if (! empty($missingKeysInEn)) {
            $this->error('The following translation keys exist in [id] but are MISSING in [en]:');
            foreach ($missingKeysInEn as $k) {
                $this->line("  - <fg=red>{$k}</>");
            }
            $hasError = true;
        }

        if ($hasError) {
            $this->error('Parity check failed: translation keys are not synchronized.');
            return Command::FAILURE;
        }

        $this->info("✓ Language parity verified! Checked {$totalKeysChecked} keys across ".count($allBasenames)." files with 0 orphan keys.");
        return Command::SUCCESS;
    }

    /**
     * Recursively flatten array keys into dot notation.
     *
     * @param  array<string, mixed>  $array
     * @return array<string>
     */
    public function flattenKeys(array $array, string $prefix = ''): array
    {
        $keys = [];

        foreach ($array as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $keys = array_merge($keys, $this->flattenKeys($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }

        return $keys;
    }
}
