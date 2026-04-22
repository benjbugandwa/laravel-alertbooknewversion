<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class BestEffort
{
    public static function run(string $context, callable $fn): void
    {
        try {
            $fn();
        } catch (\Throwable $e) {
            Log::warning("BestEffort failed: {$context}", [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            // On n'échoue pas l'action métier.
        }
    }
}
