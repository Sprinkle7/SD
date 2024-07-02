<?php

namespace App\Jobs\Cart\Invoice;

use App\Models\Invoice\InvoiceAddressProduct;
use App\Models\Invoice\InvoiceAddressProductTranslation;
use App\Models\Order\OrderAddressProduct;
use App\Models\Product\Pivot\Type2\Pt2Combination;
use App\Models\Product\Pivot\Type2\Pt2CombinationPt1Combination;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddInvoiceTranslationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $paymentIntent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($paymentIntent)
    {
        $this->paymentIntent = $paymentIntent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(2);
        $orderProducts = InvoiceAddressProduct::where('payment_intent', $this->paymentIntent)->get();
        foreach ($orderProducts as $order) {
            $product = ProductTranslation::where('product_id', $order['product_id'])->get()->mapWithKeys(function ($item) {
                return [$item['language'] => ['product_id' => $item['product_id'], 'title' => $item['title']]];
            });
            $options = @Product::fetchCombinationOptionValuesAllLanguage($order['combination_id']);
            if(!empty($options)) {
                foreach ($options as $option) {
                    if(isset($product[$option['language']])) {
                        InvoiceAddressProductTranslation::create(
                            [   'invoice_address_product_id' => $order['invoice_address_product_id'],
                                'product_id' => $product[$option['language']]['product_id'],
                                'combination_id' => $option['combination_id'],
                                'language' => $option['language'],
                                'product_title' => $product[$option['language']]['title'],
                                'options' => $option['option_values']   ]
                        );
                    }
                }
            }
        }
    }
}
