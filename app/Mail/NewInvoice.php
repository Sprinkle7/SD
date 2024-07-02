<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class NewInvoice extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $file;
    private $number;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public function __construct($user,$products,$product, $productsInvoice, $addressesInvoice, $invoiceNumber, $customproducts, $file = null)
    {
        $user['invoice_number'] = $invoiceNumber['invoice_id'];
        $user['invoice'] = $invoiceNumber;
        $user['productsInvoice'] = $productsInvoice;
        $user['addressesInvoice'] = $addressesInvoice;
        $user['products'] = $products;
        $user['product'] = $product;
        $user['custom'] = $customproducts;
        $this->user = $user;
        $this->file = $file;
        $this->number = $invoiceNumber['invoice_id'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        sleep(2);

        $mail = $this->from(env('MAIL_FROM_ADDRESS'),'Shop | sevendisplays.com')->subject('Ihre Bestellung '.$this->number.' bei sevendisplays.com');
        // $mail = $this->from(env('MAIL_FROM_ADDRESS'),'Ihre Bestellung '.$this->number.' bei sevendisplays.com');
        // if ($this->file) {
        //     $this->file = storage_path('app/OrderCsv/') . $this->file;
        //     $mail->attach($this->file, ['as' => 'invoice.csv']);
        // }
        return $mail->view('mails.newInvoice')->with([ 'user' => $this->user ]);
    }
}
