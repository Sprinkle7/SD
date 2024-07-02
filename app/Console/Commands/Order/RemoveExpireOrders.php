<?php

namespace App\Console\Commands\Order;

use App\Helper\TQ;
use App\Jobs\Cart\Order\RemovedExpiredTokenJob;
use Illuminate\Console\Command;

class RemoveExpireOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dispatch(new RemovedExpiredTokenJob());
        return 0;
    }
}
