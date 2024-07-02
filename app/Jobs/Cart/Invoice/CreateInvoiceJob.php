<?php

namespace App\Jobs\Cart\Invoice;

use App\Models\Order\ability\Ordering;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $sessionId;
    private $paymentIntent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sessionId, $paymentIntent)
    {
        $this->sessionId = $sessionId;
        $this->paymentIntent = $paymentIntent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sleep(1);
        Ordering::createInvoice($this->sessionId, $this->paymentIntent);
    }
}
