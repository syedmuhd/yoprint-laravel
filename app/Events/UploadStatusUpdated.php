<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Upload $upload) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('uploads.' . $this->upload->id);
    }

    /**
     * The name of the event to broadcast.
     */
    public function broadcastAs(): string
    {
        return 'UploadStatusUpdated';
    }
}