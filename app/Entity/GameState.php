<?php

namespace App\Entity;

class GameState
{
    public function __construct()
    {
        for ($columnNumber = 1; $columnNumber <= 3; $columnNumber++) {
            for ($rowNumber = 1; $rowNumber <= 3; $rowNumber++) {
                $this->{$columnNumber . $rowNumber} = null;
            }
        }
    }
}
