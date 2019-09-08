<?php

namespace App\Http\Services;

use App\Entity\Collection\CellCollection;
use App\Entity\Collection\GameCollection;
use App\Entity\Game;
use App\Entity\GameCell;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Exception;

class GameService
{
    public function getAbleToConnectGameList(): GameCollection
    {
        $playerId = Auth::user()->getAuthIdentifier();

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

    public function createGame(string $name, ?string $password): Game
    {
        $playerId = Auth::user()->getAuthIdentifier();

        $cellCollection = $this->createCellCollection();

        $game = new Game([
            'name'             => $name,
            'status'           => Game::STATUS_NEED_PLAYERS,
            'active_player_id' => $playerId,
            'cross_player_id'  => $playerId,
            'zero_player_id'   => null,
            'cellCollection'   => $cellCollection,
        ]);
        $game->save();

        return $game;
    }

    public function getSignByPlayer(Game $game, string $playerId): string
    {
        if ($game->cross_player_id == $playerId) {
            return Game::CROSS;
        }
        if ($game->zero_player_id == $playerId) {
            return Game::ZERO;
        }

        throw new Exception("Can't get sign");
    }

    public function update(Game $game, int $row, int $column, int $playerId): void
    {
        if ($game->status !== Game::STATUS_STARTED) {
            return;
        }

        if ($game->active_player_id != $playerId && false) {
            return;
        }

        $gameCell = $game->cellCollection->getByRowAndColumn($row, $column);
        if (!is_null($gameCell->value) && false) {
            return;
        }

        $sign = $this->getSignByPlayer($game, $playerId);

        if ($gameCell->value == $sign && false) {
            return;
        }

        $game->cellCollection = $game->cellCollection->getWithUpdatedCellValue($row, $column, $sign);

        $this->changeTurn($game, $playerId);
        $this->checkWinner($game);
        $game->save();
    }

    public function getByPlayerId(string $gameId, string $playerId): ?Game
    {
//      TODO: filter by playerId
        $game = Game::find($gameId);

        return $game;
    }

    private function createCellCollection(): CellCollection
    {
        $collection = new CellCollection();
        for ($columnNumber = 1; $columnNumber <= 3; $columnNumber++) {
            for ($rowNumber = 1; $rowNumber <= 3; $rowNumber++) {
                $cell = new GameCell($columnNumber, $rowNumber);
                $collection->push($cell);
            }
        }

        return $collection;
    }

    private function changeTurn(Game $game, string $playerId): void
    {
        if ($game->cross_player_id == $playerId) {
            $game->update(["active_player_id" => $game->zero_player_id]);
        } elseif ($game->zero_player_id == $playerId) {
            $game->update(["active_player_id" => $game->cross_player_id]);
        } else {
            throw new Exception('cannot change turn');
        }
    }

    private function checkWinner(Game $game): void
    {
        $winnerValue = $game->cellCollection->getWinner();
        if ($winnerValue) {
            $this->finishGame($game, $this->getWinnerId($game, $winnerValue));
        }
        if ($game->cellCollection->isAllCellsFilled()) {
            $this->finishGame($game, null);
        }
    }

    private function getWinnerId(Game $game, string $winnerValue): int
    {
        if ($winnerValue == Game::CROSS) {
            return $game->cross_player_id;
        } elseif ($winnerValue == Game::ZERO) {
            return $game->zero_player_id;
        } else {
            throw new Exception('cannot change turn');
        }
    }

    private function finishGame(Game $game, ?int $winnerID): void
    {
        if ($winnerID) {
            $game->update(["winner_id" => $winnerID]);
        }
        $game->update(["status" => Game::STATUS_ENDED]);
    }

    public function joinGame(Game $game, string $playerId): void
    {
        if ($this->alreadyJoined($game, $playerId)) {
            return;
        }
        if (!$this->isAbleToJoinGame($game)) {
            throw new Exception('Unable to join game');
        }

        $this->addPlayer($game, $playerId);
        $game->status = Game::STATUS_STARTED;
        $game->save();
    }

    private function isAbleToJoinGame(Game $game): bool
    {
        if ($game->status == Game::STATUS_NEED_PLAYERS) {
            return true;
        }

        return false;
    }

    private function alreadyJoined(Game $game, string $playerId): bool
    {
        return $game->cross_player_id == $playerId || $game->zero_player_id == $playerId;
    }

    private function addPlayer(Game $game, int $playerId): void
    {
        if (is_null($game->cross_player_id)) {
            $game->cross_player_id = $playerId;
        } elseif (is_null($game->zero_player_id)) {
            $game->zero_player_id = $playerId;
        } else {
            throw new Exception('cannot create sign for game: '.$game->id);
        }
    }
}
