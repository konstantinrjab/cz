<?php

namespace App\Events;

use App\Entity\Game;
use App\Http\Services\GameRepresentationService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class GameChangeStateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var Game $game */
    private $game;

    /** @var GameRepresentationService $representationService */
    private $representationService;

    public function __construct(Game $game, GameRepresentationService $representationService)
    {
        $this->game = $game;
        $this->representationService = $representationService;
    }

    public function broadcastAs()
    {
        return 'GameChangeStateEvent';
    }

    public function broadcastOn(): Channel
    {
        return new Channel('game.'.$this->game->id);
    }

    public function broadcastWith()
    {
        return $this->representationService->getGame($this->game);
    }
}
