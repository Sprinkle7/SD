<?php

namespace App\Jobs\Cart\Invoice;

use App\Helper\InvoiceCSV\GenerateCSVRow;
use App\Helper\InvoiceCSV\InvoiceCSV;
use App\Mail\NewInvoice;
use App\Models\Duration\DurationTranslation;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddress;
use App\Models\Invoice\InvoiceAddressProduct;
use App\Models\Product\Product;   
use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Models\Order\CustomOrder;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class GenerateCSVJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $payment_intent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payment_intent)
    {
        $this->payment_intent = $payment_intent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(2);
        // $cSVRow = new GenerateCSVRow();
        // $invoiceCsv = new InvoiceCSV();
        $invoice = Invoice::where('payment_intent', $this->payment_intent)->first();
        $invoiceCount = Invoice::where('user_id', $invoice['user_id'])->count();
        if ($invoiceCount > 0) {
            $invoice['comment'] = 'Bestandskunde(' . ($invoiceCount + 1) . ')';
        } else {
            $invoice['comment'] = 'Neuekunde';
        }
        
        $user = User::find($invoice['user_id'])->toArray();
        $user['country_name'] = $invoice['country_name'];
        // $cSVRow->setInvoiceInfo($user, $invoice);
        $addressesInvoice = InvoiceAddress::with(['post' => function ($query) {
            $query->where('language', 'de');
        }])->where('payment_intent', $this->payment_intent)->get();

        $productsInvoice = InvoiceAddressProduct::where('payment_intent', $this->payment_intent)->get()->toArray();
        $products = [];
        $product = [];
        $x = 0;

        foreach ($productsInvoice as $productIn) {
            foreach ($productIn['services_data'] as $servive) {
                $productIn['product_title'] = $productIn['product_title'] . ' || ' . $servive['service_value_translation']['title'];
            }
            $duration = DurationTranslation::where('duration_id', $productIn['duration_id'])->where('language', 'de')->first();
            $productIn['product_title'] = $productIn['product_title'] . ' || ' . $duration['title'];
            $prod = Product::find($productIn['product_id']);
            $productIn['product'] = $prod;
            $products[$productIn['order_address_id']] = $productIn;
            $products[$productIn['order_address_id']]['product'] = $prod;
            $product[$x] = $productIn;
            $product[$x]['product'] = $prod;
            $x++;
        }

        foreach ($addressesInvoice as $addressInc) {
            $address = User\Address::find($addressInc['address_id']);
            if ($address) {
                $address['country_name'] = $addressInc['country_name'];
            } else {
                $address = $user;
            }

            $query = 'SELECT cpd.id as post_duration_id,cpd.post_id,pmt.title as \'post_method\',cpd.min_price,cpd.price,pmt.post_method_id FROM country_post_duration cpd  ' . 'JOIN post_method_translations pmt ON cpd.post_id=pmt.post_method_id WHERE cpd.post_id = ' . $addressInc['post_id'] . ' ORDER BY cpd.price';
            $postMethod = DB::select(DB::raw($query));
            $addressInc['address'] = $address;
            $addressInc['shippings'] = $postMethod;
            // $cSVRow->setShippingAddress($address, $addressInc);
            $addressInc['pros'] = @$products[$addressInc['order_address_id']]['product'];
            // $cSVRow->setproduct(@$products[$addressInc['order_address_id']]['product'],@$products[$addressInc['order_address_id']]);
            // $invoiceCsv->setInvoice([$cSVRow->generateRow()]);
        }

        $customproducts = CustomOrder::where('payment_intent', $invoice->payment_intent)->first();
       
        
        // $file = $invoiceCsv->generaetCSV($invoice['invoice_id']);
        $file = '';
        // Invoice::where('payment_intent', $this->payment_intent)->update(['file' => $file]);
        // email to admin
        // $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        // s.khan@golfleads.de
        
        Mail::mailer('smtp')->to('it@sevendisplays.com')
            ->bcc('it@sevendisplays.com')
            ->queue((new NewInvoice($user,$product,$products,$productsInvoice,$addressesInvoice, $invoice, $customproducts,$file))->subject('Auftragsbestätigung')->onQueue('mail'));
        // email to user
        Mail::mailer('smtp')->to($user['email'])->queue((new NewInvoice($user,$product,$products,$productsInvoice,$addressesInvoice, $invoice,  $customproducts,$file))->subject('Auftragsbestätigung')->onQueue('mail'));
    }
}