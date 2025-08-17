<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
class PublishOrderPlacedEvent implements ShouldQueue
{


    /**
     * Create a new job instance.
     */

     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct( public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Yahan hum event publish karne ka logic likhein ge.
        // Abhi ke liye, hum bas log file mein likh kar test karein ge.
        Log::info('Order Placed Event Published for Order ID: ' . $this->order->id, $this->order->load('items')->toArray());
    }
}
