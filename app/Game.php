<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Game extends Eloquent
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

    public function getSign($userID)
    {
        if ($this['players'][self::CROSS] == $userID) return self::CROSS;
        if ($this['players'][self::ZERO] == $userID) return self::ZERO;
        throw new \LogicException("Can't get your sign");
    }

    public function changeTurn($userID)
    {
        if ($this['players'][self::CROSS] == $userID) {
            $nexTurnUserID = $this['players'][self::ZERO];
        }
        if ($this['players'][self::ZERO] == $userID) {
            $nexTurnUserID = $this['players'][self::CROSS];
        }

        $this->update(["players.turn" => $nexTurnUserID], ['upsert' => true]);
    }

    public function checkWinner()
    {
        foreach ($this->winningCombinations as $combination) {
            foreach ($combination as $position) {
                if (!is_numeric($this['state'][$position])) {
                    $sign = null;
                    $secondInRow = false;
                    break;
                }

                if (!isset($sign)) {
                    $sign = $this['state'][$position];
                    continue;
                }

                if ($sign != $this['state'][$position]) {
                    $sign = null;
                    $secondInRow = false;
                    break;
                }

                if (!isset($secondInRow)) {
                    $secondInRow = true;
                    continue;
                }

                $this['winner'] = $this['players'][$sign];
            }
        }
    }
}
