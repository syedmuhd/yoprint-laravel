<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportChunkProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The Upload model instance.
     *
     * @var \App\Models\Upload
     */
    public $upload;

    /**
     * The percentage of completion.
     *
     * @var int
     */
    public $progress;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Upload $upload, int $progress)
    {
        $this->upload = $upload;
        $this->progress = $progress;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Broadcast on a private channel specific to the upload
        return new Channel('uploads.'.$this->upload->id);
    }

    /**
     * The name of the event to broadcast.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'ImportProgressUpdated';
    }
}