<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationToastEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        protected Model|Authenticatable $user,
        public string $title,
        public ?string $body = null,
        public ?string $icon = null,
        public ?string $iconColor = null,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        $userClass = str_replace('\\', '.', $this->user::class);

        return new PrivateChannel("{$userClass}.{$this->user->getKey()}");
    }

    public function broadcastAs(): string
    {
        return 'notification-toast.received';
    }
}
