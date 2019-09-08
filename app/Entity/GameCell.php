<?php

namespace App\Entity;

class GameCell
{
    /** @var int $column */
    public $column;

    /** @var int $row */
    public $row;

    /** @var ?string $value */
    public $value = null;

    public function __construct(int $column, int $row)
    {
        $this->column = $column;
        $this->row = $row;
    }
}
