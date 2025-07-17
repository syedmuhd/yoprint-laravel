<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UploadStatusUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Upload $upload) {}

    public function broadcastOn(): Channel
    {
        return new Channel('uploads');
    }

    public function broadcastWith(): array
    {
        return [
            'upload' => $this->upload->toArray(),
        ];
    }
}
