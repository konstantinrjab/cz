<?php

namespace App\Http\Services;

use App\Entity\Game;
use App\Http\Collections\GameCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Builder;
use StdClass;
use Exception;

class GameService
{
    public function getAbleToConnectList(): GameCollection
    {
        $query = Game::query()->where('status', Game::NEED_PLAYERS)
            ->orWhere(function (Builder $qb) {
                $qb->where('status', Game::STARTED)
                    ->where(function (Builder $qb) {
                        $userId = Auth::user()->getAuthIdentifier();
                        $qb->where('players.'.Game::CROSS, $userId)
                            ->orWhere('players.'.Game::ZERO, $userId);
                    });
            });

        /** @var GameCollection $gameCollection */
        $gameCollection = $query->get();

        return $gameCollection;
    }

    public function createGame(string $name, ?string $password): Game
    {
        $userId = Auth::user()->getAuthIdentifier();

        $state = $this->createState();
        $players = $this->createPlayers($userId);

        $game = new Game([
            'status'   => Game::NEED_PLAYERS,
            'name'     => $name,
            'password' => $password,
            'winner'   => null,
            'players'  => $players,
            'state'  => $state,
        ]);

        $game->save();

        return $game;
    }

    public function getSign(Game $game, string $userID): int
    {
        if ($game['players'][Game::CROSS] == $userID) return Game::CROSS;
        if ($game['players'][Game::ZERO] == $userID) return Game::ZERO;

        throw new Exception("Can't get sign");
    }

    public function update(Request $request, Game $game, string $userId): void
    {
        if ($game['status'] !== Game::STARTED) {
            return;
        }

        if ($game['players']['turn'] != $userId) {
            return;
        }

        $cell = $request->cell;

        if (!is_null($game['state'][$cell])) {
            return;
        }

        $sign = $this->getSign($game, $userId);

        if ($cell == $sign) {
            return;
        }

        $game->update(["state.$request->cell" => $sign]);

        $game->changeTurn($userId);
        $game->checkWinner();
        $game->save();
    }

    public function getByPlayerId(string $gameId, string $playerId): ?Game
    {
        $game = Game::query()->find($gameId);
//      TODO: filter by playerId

        return $game;
    }

    private function createState(): StdClass
    {
        $state = new StdClass();
        for ($columnNumber = 1; $columnNumber <= 3; $columnNumber++) {
            for ($rowNumber = 1; $rowNumber <= 3; $rowNumber++) {
                $state->{$columnNumber.$rowNumber} = null;
            }
        }

        return $state;
    }

    private function createPlayers(string $userId): StdClass
    {
        $players = new StdClass();
        $players->turn = $userId;
        $players->{1} = $userId;
        $players->{0} = null;

        return $players;
    }
}
