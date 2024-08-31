<?php

namespace App\Events;

use App\Models\Note;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NoteSucessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Note $note)
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
