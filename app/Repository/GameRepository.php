<?php

namespace App\Repository;

use App\Entity\Collection\CellCollection;
use App\Entity\Collection\GameCollection;
use App\Entity\Game;
use Illuminate\Database\Eloquent\Builder;

class GameRepository
{
    public function getAbleToConnectByPlayerId(int $playerId): GameCollection
    {
        $query = Game::query()->where('status', Game::STATUS_NEED_PLAYERS)
            ->orWhere(function (Builder $qb) use ($playerId) {
                $qb
                    ->where('status', Game::STATUS_STARTED)
                    ->where(function (Builder $qb) use ($playerId) {

                        $qb->where('cross_player_id', $playerId)
                            ->orWhere('zero_player_id', $playerId);
                    });
            });

        /** @var GameCollection $gameCollection */
        $gameCollection = $query->get();

        return $gameCollection;
    }

    public function create(string $name, string $password, int $playerId, CellCollection $cellCollection): Game
    {
        $game = new Game([
            'name'             => $name,
            'password'             => $password,
            'status'           => Game::STATUS_NEED_PLAYERS,
            'active_player_id' => $playerId,
            'cross_player_id'  => $playerId,
            'zero_player_id'   => null,
            'cellCollection'   => $cellCollection,
        ]);
        $game->save();

        return $game;
    }

    public function getByIdAndPlayerId(int $gameId, int $playerId): ?Game
    {
        $game = Game::query()
            ->where('id', $gameId)
            ->where(function (Builder $qb) use ($playerId) {
                $qb
                    ->where(function (Builder $qb) use ($playerId) {
                        $qb->where('cross_player_id', $playerId)
                            ->orWhere('zero_player_id', $playerId);
                    });
            })
            ->first();

        return $game;
    }
}
