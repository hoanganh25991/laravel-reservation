<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ShouldUpdateCacheDatesWithAvailableTimeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $outlet_id;

    /**
     * Create a new event instance.
     *
     * @param $outlet_id
     */
    public function __construct($outlet_id)
    {
        $this->outlet_id = $outlet_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
