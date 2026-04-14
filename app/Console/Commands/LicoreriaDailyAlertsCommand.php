<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LicoreriaDailyAlertsCommand extends Command
{
    protected $signature = 'licoreria:daily-alerts';

    protected $description = 'Alertas de fin de día: productos con stock bajo (y registro en log).';

    public function handle(): int
    {
        $low = Product::query()->lowStock()->get();

        foreach ($low as $product) {
            $msg = sprintf(
                'Stock bajo: %s (mínimo %s, actual %s)',
                $product->name,
                $product->min_stock_alert,
                $product->stock_quantity
            );
            Log::warning($msg);
            $this->warn($msg);
        }

        if ($low->isEmpty()) {
            $this->info('No hay productos bajo el mínimo configurado.');
        }

        return self::SUCCESS;
    }
}
