<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Prajwal89\Analytics\Models\PageView;

class NewPageViewEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PageView $pageview) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
