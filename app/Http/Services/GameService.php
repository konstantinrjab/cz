<?php

namespace App\Http\Services;

use App\Game;
use App\Http\Collections\GameCollection;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Builder;

class GameService
{
    public function getAbleToConnectList(): GameCollection
    {
        $query = Game::query()->where('status', Game::NEED_PLAYERS)
            ->orWhere(function (Builder $qb) {
                $qb->where('status', Game::STARTED)
                    ->where(function (Builder $qb) {
                        $userId = Auth::user()->getAuthIdentifier();
                        $qb->where('players.' . Game::CROSS, $userId)
                            ->orWhere('players.' . Game::ZERO, $userId);
                    });
            });

        /** @var GameCollection $gameCollection */
        $gameCollection = $query->get();

        return $gameCollection;
    }
}
