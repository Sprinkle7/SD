<?php

namespace App\Jobs\Cart\Invoice;

use App\Helper\InvoiceXml\AddressXml;
use App\Helper\InvoiceXml\InvoiceXml;
use App\Helper\InvoiceXml\ProductXml;
use App\Helper\InvoiceXml\UserXml;
use App\Models\Duration\DurationTranslation;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddress;
use App\Models\Invoice\InvoiceAddressProduct;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateXmlJob implements ShouldQueue
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
        $info = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<tBestellungen>
</tBestellungen>
XML;
        $invoice = Invoice::where('payment_intent', $this->payment_intent)->first();
        $addresses = InvoiceAddress::where('payment_intent', $this->payment_intent)->get();
        $productsInvoice = InvoiceAddressProduct::where('payment_intent', $this->payment_intent)->get();
        $user = User::find($invoice['user_id']);
        $user['country'] = $invoice['country_name'];
        $invoiceXml = new InvoiceXml();
        $addressXml = new AddressXml();
        $productXml = new ProductXml();
        $userXml = new UserXml();
        foreach ($addresses as $address) {
            $xml = new \SimpleXMLElement($info);
            $products = [];
            $invoice['id'] = $address['id'];
            foreach ($productsInvoice as $product) {
                if ($address['order_address_id'] == $product['order_address_id']) {
                    $invoice['duration'] = $product['duration'];
                    $products[] = $product;
                }
            }

            
            $invoice['shipping_date'] = now()->addDays($invoice['duration'])->format('Y-m-d');
            $invoiceXml->setObject($invoice);
            $xml = $invoiceXml->generate($xml);

            $maxServiceDuration = 0;

            foreach ($products as $product) {

                $servicesDuration = 0;
                foreach ($product['services_data'] as $servive) {
                    $product['product_title'] = $product['product_title'] . ' || '
                        . $servive['service_value_translation']['title'];
                    $servicesDuration += $servive['duration'];
                }

                $duration = DurationTranslation::where('duration_id', $product['duration_id'])->where('language', 'de')->first();

                $product['product_title'] = $product['product_title'] . ' || ' . $duration['title'];

                $productXml->setObject($product);
                $xml = $productXml->generate($xml);
                if ($servicesDuration > $maxServiceDuration) {
                    $maxServiceDuration = $servicesDuration;
                }
            }
            $invoice['shipping_date'] = now()->addDays($invoice['duration'] + $maxServiceDuration)->format('Y-m-d');
            $invoiceXml->setObject($invoice);
            $invoiceXml->updateShippingDate($xml);

            $userXml->setObject($user);
            $xml = $userXml->generate($xml);
            $shippingAddress = [];
            if ($address['address_id'] == null) {
                $shippingAddress = $user;
            } else {
                $shippingAddress = User\Address::with(['country.translation' => function ($query) {
                    $query->where('language', 'de');
                }])->find($address['address_id']);

                $shippingAddress['country'] = $shippingAddress['country']['translation']['name'];
            }
            Storage::disk('local')->put('OrderCsv/bbbb.txt', 'sdfdf');

            $addressXml->setObject($shippingAddress);
            $xml = $addressXml->generate($xml);

            $name = uniqid() . time() . '.xml';
            $xml->saveXML(storage_path('app/OrderCsv/').$name);
            InvoiceAddress::where('id', $address['id'])->update(['xml_invoice' => $name]);
        }
    }
}
