<?php

namespace App\Entity;

use App\Http\Collections\GameCollection;
use Jenssegers\Mongodb\Eloquent\Model;

class Game extends Model
{
    public const NEED_PLAYERS = 2;
    public const STARTED = 3;
    public const ENDED = 4;

    public const CROSS = 1;
    public const ZERO = 0;

    protected $connection = 'mongodb';
    protected $collection = 'game';

    protected $guarded = [];
    protected $hidden = ['password'];

    public function newCollection(array $models = []): GameCollection
    {
        return new GameCollection($models);
    }
}
