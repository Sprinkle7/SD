<?php

namespace App\Jobs\Cart;

use App\Models\Cart\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactiveCartItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;
    private $sessionInfo;
    private $orderProducts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $sessionInfo, $orderProducts)
    {
        $this->userId = $userId;
        $this->sessionInfo = $sessionInfo;
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

            Cart::where('user_id', $this->userId)->where('product_id', $order['product_id'])
                ->where('combination_id', $order['combination_id'])->where('duration_id', $order['duration_id'])
                ->where('services', $order['services'])
                ->update(['session_id' => $this->sessionInfo['id'], 'is_active' => 0]);
        }

    }
}
