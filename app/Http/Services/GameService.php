<?php

namespace App\Http\Services;

use App\Entity\Game;
use App\Http\Collections\GameCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Eloquent\Builder;
use StdClass;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GameService
{
    private const WINNING_COMBINATIONS = [
        [11, 12, 13],
        [11, 22, 33],
        [11, 21, 31],
        [12, 22, 32],
        [13, 22, 31],
        [13, 23, 33],
        [21, 22, 23],
        [31, 32, 33]
    ];

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
            'state'    => $state,
        ]);

        $game->save();

        return $game;
    }

    public function getSign(Game $game, string $playerId): int
    {
        if ($game['players'][Game::CROSS] == $playerId) return Game::CROSS;
        if ($game['players'][Game::ZERO] == $playerId) return Game::ZERO;

        throw new Exception("Can't get sign");
    }

    public function update(Request $request, Game $game, string $playerId): void
    {
        if ($game['status'] !== Game::STARTED) {
            return;
        }

        if ($game['players']['turn'] != $playerId) {
            return;
        }

        $cell = $request->cell;

        if (!is_null($game['state'][$cell])) {
            return;
        }

        $sign = $this->getSign($game, $playerId);

        if ($cell == $sign) {
            return;
        }

        $game->update(["state.$request->cell" => $sign]);

        $this->changeTurn($game, $playerId);
        $this->checkWinner($game);
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

    private function changeTurn(Game $game, string $userID): void
    {
        if ($game['players'][Game::CROSS] == $userID) {
            $nexTurnUserID = $game['players'][Game::ZERO];
        }
        if ($game['players'][Game::ZERO] == $userID) {
            $nexTurnUserID = $game['players'][Game::CROSS];
        }

        $game->update(["players.turn" => $nexTurnUserID]);
    }

    private function checkWinner(Game $game): void
    {
        if ($this->isAllFieldsFilled($game)) {
            $this->finishGame(null);
        }

        foreach (self::WINNING_COMBINATIONS as $combination) {
            foreach ($combination as $position) {
                if (!is_numeric($this['state'][$position])) {
                    $sign = null;
                    $secondInRow = null;
                    break;
                }

                if (!isset($sign)) {
                    $sign = $this['state'][$position];
                    continue;
                }

                if ($sign != $this['state'][$position]) {
                    $sign = null;
                    $secondInRow = null;
                    break;
                }

                if (!isset($secondInRow)) {
                    $secondInRow = true;
                    continue;
                }

                if ($secondInRow === true) {
                    $this->endGame($this['players'][$sign]);
                }
            }
        }
    }

    private function isAllFieldsFilled(Game $game): bool
    {
        for ($rowNumber = 0; $rowNumber < 3; $rowNumber++) {
            for ($columnNumber = 0; $columnNumber < 3; $columnNumber++) {
                if ($game['state'][$rowNumber.$columnNumber] === null) {
                    return false;
                }
            }
        }

        return true;
    }

    private function finishGame(Game $game, ?string $winnerID): void
    {
        if ($winnerID) {
            $game->update(["winner" => $winnerID]);
        }
        $game->update(["status" => Game::ENDED]);
    }

    public function isAbleToJoinGame(Game $game): bool
    {
        if ($game->status == Game::NEED_PLAYERS) {
            return true;
        }

        return false;
    }

    public function joinGame(Game $game, string $playerId): void
    {
        if ($this->isAbleToJoinGame($game)) {
            throw new BadRequestHttpException('Unable to join game');
        }
        if ($this->alreadyJoined($game, $playerId)) {
            return;
        }
        $sign = $this->createSign($game);

        $game->update(["players.".$sign => $playerId]);
        $game->update(["status" => Game::STARTED]);
    }

    private function alreadyJoined(Game $game, string $playerId): bool
    {
        return $game['players'][Game::CROSS] == $playerId || $game['players'][Game::ZERO] == $playerId;
    }

    private function createSign(Game $game): int
    {
        if (empty($game['players'][Game::CROSS])) {
            return Game::CROSS;
        }
        if (empty($game['players'][Game::ZERO])) {
            return Game::ZERO;
        }

        throw new \Exception('cannot create sign for game: '.$game->_id);
    }
}
