<?php

namespace App\Console\Commands;

use App\Services\OrderStatusConsumer;
use Illuminate\Console\Command;

class MQConsumerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mq:consume {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Consummer';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $mqConsumer = new OrderStatusConsumer();
        
        if($this->argument('status') == 'pickup') 
        {
            $mqConsumer->consumePickup();
        } 
        else if($this->argument('status') == 'confirm') 
        {
            $mqConsumer->consumeConfirm();
        }
    }
}
