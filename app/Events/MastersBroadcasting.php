<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MastersBroadcasting implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $master_type;
    public $data;
    public $operation;
    public $operation_text;
    private $channel;

    /**
     * Create a new event instance.
     *
     * @param $payload - pass payload data
     * @param $operation - operation type enum
     * @param null $channel - channel name if needed ( default: 'manage-masters' )
     */
    public function __construct($payload, $operation, $channel = null)
    {
        if (is_null($channel))
            $channel = 'manage-masters';

        $this->data = $payload;
        $this->master_type = $payload->getTable();
        $this->operation = $operation;
        $this->operation_text = config('constants.broadcasting.operation.' . $operation);
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->channel);
    }
}
