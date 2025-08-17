<?php

namespace App\Events;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;


    public function __construct($order)
    {
        $this->orderId = $order;

    }

    public function broadcastOn()
    {
        return new Channel('orders.' . auth()->user()->socket_token);
    }

    public function broadcastAs()
    {
        return 'status.updated';
    }
}
