<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnNoteNotificationShown
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $id)
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
