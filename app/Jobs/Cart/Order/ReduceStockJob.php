<?php

namespace App\Jobs\Cart\Order;

use App\Models\Product\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReduceStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $orderProducts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderProducts)
    {
        $this->orderProducts = $orderProducts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->orderProducts as $order) {
            $stocks = Product::fetchStockAmount($order['product_id'], $order['combination_id']);
            foreach ($stocks as $stock) {
                if ($stock['stock'] != null) {
                    DB::table('option_value_product')
                        ->where('product_id', $order['product_id'])
                        ->where('option_value_id', $stock['option_value_id'])
                        ->update(['stock' => $stock['stock'] - $order['quantity']]);
                }
            }
        }
    }
}
