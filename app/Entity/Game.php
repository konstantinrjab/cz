<?php

namespace App\Entity;

use App\Entity\Collection\CellCollection;
use App\Entity\Collection\GameCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Game
 * @package App\Entity
 *
 * @method static self find(int $id)
 *
 * @property int $id
 * @property string $name
 * @property int $cross_player_id
 * @property int $zero_player_id
 * @property int $status
 * @property int $active_player_id
 * @property int $winner_id
 * @property string $password
 * @property CellCollection $cellCollection
 */
class Game extends Model
{
    public const STATUS_NEED_PLAYERS = 1;
    public const STATUS_STARTED = 2;
    public const STATUS_ENDED = 3;

    public const CROSS = 'cross';
    public const ZERO = 'zero';

    protected $guarded = [];
    protected $hidden = ['password'];

    public function getCellCollectionAttribute($value): CellCollection
    {
        if ($value instanceof CellCollection) {
            return $value;
        }

        return unserialize($value);
    }

    public function newCollection(array $models = []): GameCollection
    {
        return new GameCollection($models);
    }

    public function save(array $options = [])
    {
        if ($this->cellCollection instanceof CellCollection) {
            $this->cellCollection = serialize($this->cellCollection);
        }

        return parent::save($options);
    }
}
