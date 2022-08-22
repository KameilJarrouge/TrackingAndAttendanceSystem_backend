<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GsRestartEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gsId;
    public $restart_start_time;
    public $restart_duration;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($gsId, $restart_start_time, $restart_duration)
    {
        $this->gsId = $gsId;
        $this->restart_start_time = $restart_start_time;
        $this->restart_duration = $restart_duration;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('pythonChannel');
    }
}
