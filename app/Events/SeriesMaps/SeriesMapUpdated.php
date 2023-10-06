<?php

namespace App\Events\SeriesMaps;

use App\Models\SeriesMap;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeriesMapUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $seriesId;

    /**
     * Create a new event instance.
     */
    public function __construct(public int $seriesMapId)
    {
        $this->seriesId = SeriesMap::findOrFail($seriesMapId)->series_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('series-maps-' . $this->seriesMapId),
            new Channel('series-' . $this->seriesId)
        ];
    }
}
