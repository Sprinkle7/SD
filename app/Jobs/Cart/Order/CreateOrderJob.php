<?php

namespace App\Jobs\Cart\Order;

use App\Jobs\Cart\DeactiveCartItemJob;
use App\Jobs\Cart\Invoice\CreateInvoiceJob;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use App\Models\Order\CustomOrder;
use App\Models\Order\OrderAddressProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sessioninfo;
    private $order;
    private $orderAddresses;
    private $orderAddressProducts;
    private $myproducts;
    private $summary;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sessioninfo, $order, $orderAddresses, $orderAddressProducts,$myproducts, $summary)
    {
        $this->sessioninfo = $sessioninfo;
        $this->order = $order;
        $this->orderAddresses = $orderAddresses;
        $this->orderAddressProducts = $orderAddressProducts;
        $this->myproducts = $myproducts;
        $this->summary = $summary;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sessionId = $this->sessioninfo['id'];
        $paymentIntent = $this->sessioninfo['payment_intent'];
        $this->order['session_id'] = $sessionId;
        $this->order['payment_intent'] = $paymentIntent;
        $this->order['expires_at'] = now()->addMinutes(15);
        Order::create($this->order);
        foreach ($this->orderAddresses as &$orderAddress) {
            $orderAddress['session_id'] = $sessionId;
        }
        OrderAddress::insert($this->orderAddresses);
        foreach ($this->orderAddressProducts as &$product) {
            $product['session_id'] = $sessionId;
        }
        OrderAddressProduct::insert($this->orderAddressProducts);
        //job to reduce product stock
        dispatch(new ReduceStockJob($this->orderAddressProducts));
        ///update cart items session_id and activation
        dispatch(new DeactiveCartItemJob($this->order['user_id'], $this->sessioninfo, $this->orderAddressProducts));

        $orderData = new CustomOrder();
        $orderData->products = json_encode($this->myproducts);
        $orderData->summary = json_encode($this->summary);
        $orderData->payment_intent = $paymentIntent;
        $orderData->session_info = $sessionId;
        $orderData->save();
        if ($this->order['payment_type'] == 'prePaid' || $this->order['payment_type'] == 'postPaid') {
            dispatch(new CreateInvoiceJob($this->sessioninfo['id'], $this->sessioninfo['payment_intent']));
        }
    }
}
