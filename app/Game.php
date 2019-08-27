<?php

namespace App;

use App\Http\Collections\GameCollection;
use Jenssegers\Mongodb\Eloquent\Model;

class Game extends Model
{
    const CREATED = 1;
    const NEED_PLAYERS = 2;
    const STARTED = 3;
    const ENDED = 4;

    const CROSS = 1;
    const ZERO = 0;

    protected $connection = 'mongodb';
    protected $collection = 'game';

    protected $guarded = [];
    protected $hidden = ['password'];

    private $winningCombinations = [
        [11, 12, 13],
        [11, 22, 33],
        [11, 21, 31],
        [12, 22, 32],
        [13, 22, 31],
        [13, 23, 33],
        [21, 22, 23],
        [31, 32, 33]
    ];

    public function getSign($userID): int
    {
        if ($this['players'][self::CROSS] == $userID) return self::CROSS;
        if ($this['players'][self::ZERO] == $userID) return self::ZERO;

        throw new \LogicException("Can't get your sign");
    }

    public function changeTurn($userID): void
    {
        if ($this['players'][self::CROSS] == $userID) {
            $nexTurnUserID = $this['players'][self::ZERO];
        }
        if ($this['players'][self::ZERO] == $userID) {
            $nexTurnUserID = $this['players'][self::CROSS];
        }

        $this->update(["players.turn" => $nexTurnUserID]);
    }

    public function checkWinner(): void
    {
        if (!$this->checkCanContinue()) {
            $this->endGame();
        }

        foreach ($this->winningCombinations as $combination) {
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

    private function endGame($winnerID = null): void
    {
        if ($winnerID) {
            $this->update(["winner" => $winnerID]);
        }
        $this->update(["status" => self::ENDED]);
    }

    public function checkCanContinue(): bool
    {
        try {
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 3; $j++) {
                    if ($this['state'][$i . $j] === null) {
                        throw new \Exception('');
                    }
                }
            }
        } catch (\Exception $exception) {
            return true;
        }

        return false;
    }

    public function newCollection(array $models = []): GameCollection
    {
        return new GameCollection($models);
    }
}
