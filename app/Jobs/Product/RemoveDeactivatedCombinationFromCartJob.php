<?php

namespace App\Jobs\Product;

use App\Models\Cart\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveDeactivatedCombinationFromCartJob implements ShouldQueue
{
    private $combinationId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($combinationId)
    {
        $this->combinationId = $combinationId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Cart::where('combination_id', $this->combinationId)->delete();
    }
}
