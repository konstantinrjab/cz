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

    public function getSign($userID)
    {
        if ($this['players']['cross'] == $userID) return self::CROSS;
        if ($this['players']['zero'] == $userID) return self::ZERO;
        throw new \LogicException("Can't set your sign");
    }

    public function changeTurn($userID)
    {
        if ($this['players']['cross'] == $userID) {
            $nexTurnUserID = $this['players']['zero'];
        }
        if ($this['players']['zero'] == $userID) {
            $nexTurnUserID = $this['players']['cross'];
        }

        $this->update(["players.turn" => $nexTurnUserID], ['upsert' => true]);
    }
}
