<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OnNotificationClicked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
        OnNotificationShown::broadcast(700);

        Log::info('chatbuddy');

        openWindow('chatbuddy', 'chat-buddy');
    }

    public function broadcastOn(): array
    {


        return [
            new PrivateChannel('nativephp'),
        ];
    }
}
