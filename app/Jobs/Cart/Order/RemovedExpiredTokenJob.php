<?php

namespace App\Jobs\Cart\Order;

use App\Helper\Payment\PayPalHelper;
use App\Helper\Payment\StripeHelper;
use App\Models\Order\FailedOrder;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RemovedExpiredTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Order::where('expires_at', '<', now())->get();


        foreach ($orders as $index => $order) {
            try {
                if ($order['payment_type'] == 'stripe') {
                    StripeHelper::cancelPaymentIntent($order['session_id']);
                } else {
                    PayPalHelper::cancelOrder($order['session_id']);
                    FailedOrder::whenOrderCancelORExpire($order['payment_intent']);
                }
            } catch (\Exception $exception) {
                FailedOrder::whenOrderCancelORExpire($order['payment_intent']);
            }
        }
    }
}
