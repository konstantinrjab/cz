<?php

namespace App\Http\Services;

use App\Entity\Collection\GameCollection;
use App\Entity\Game;

class GameRepresentationService
{
    public function getGame(Game $game): array
    {
        return [
            'id'               => $game->id,
            'active_player_id' => $game->active_player_id,
            'cross_player_id'  => $game->cross_player_id,
            'zero_player_id'   => $game->zero_player_id,
            'winner_id'        => $game->winner_id,
            'cell_collection'  => $game->cellCollection,
        ];
    }

    public function getGameCollection(GameCollection $gameCollection): array
    {
        $games = [];
        foreach ($gameCollection->all() as $game) {
            $games[] = [
                'id' => $game->id,
                'name' => $game->name,
                'cross_player_id' => $game->cross_player_id,
                'zero_player_id' => $game->zero_player_id,
            ];
        }

        return $games;
    }
}
