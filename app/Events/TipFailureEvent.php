<?php

namespace App\Events;

use App\Models\Tip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TipFailureEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Tip $tip)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
