<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceStatus extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $invoiceNumber, $message)
    {
        $this->user = $user->toArray();
        $this->user['invoice_number'] = $invoiceNumber;
        $this->user['message'] = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from(env('MAIL_FROM_ADDRESS'), 'Shop | sevendisplays.com');
        return $mail->view('mails.invoiceStatus')->with(['user' => $this->user]);
    }
}
