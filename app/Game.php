<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Game extends Eloquent
{
    const CREATED = 1;
    const NEED_PLAYERS = 2;
    const STARTED = 3;
    const ENDED = 4;

    protected $connection = 'mongodb';
    protected $collection = 'game';
}
