<?php

namespace App\Entity\Collection;

use App\Entity\GameCell;
use Illuminate\Support\Collection;

class CellCollection extends Collection
{
    public function getByRowAndColumn(int $row, int $column): GameCell
    {
        return $this
            ->where('row', $row)
            ->where('column', $column)
            ->first();
    }

    public function getWithUpdatedCellValue(int $row, int $column, string $value): self
    {
        $cell = $this->getByRowAndColumn($row, $column);
        $cell->value = $value;

        return $this;
    }

    public function isAllCellsFilled(): bool
    {
        foreach ($this as $cell) {
            if (is_null($cell->value)) {
                return false;
            }
        }

        return true;
    }

    public function getWinner(): ?string
    {
        $winner = null;
        $winner = $this->getWinnerByMainDiagonal() ?? $this->getWinnerBySideDiagonal();
        if ($winner) {
            return $winner;
        }

        for ($line = 1; $line <= $this->getSize(); $line++) {
            $winner = $this->getWinnerByRow($line) ?? $this->getWinnerByColumn($line);
            if ($winner) {
                return $winner;
            }
        }

        return null;
    }

    private function getSize(): int
    {
        return sqrt($this->count());
    }

    private function getWinnerByColumn(string $columnNumber): ?string
    {
        $columns = $this->where('column', $columnNumber);

        return $this->getWinnerValue($columns);
    }

    private function getWinnerByRow(string $rowNumber): ?string
    {
        $rows = $this->where('row', $rowNumber);

        return $this->getWinnerValue($rows);
    }

    private function getWinnerByMainDiagonal(): ?string
    {
        $mainDiagonal = new static;
        for ($row = 1; $row <= $this->getSize(); $row++) {
            $cell = $this
                ->where('row', $row)
                ->where('column', $row)
                ->first();

            $mainDiagonal->push($cell);
        }

        if ($winner = $this->getWinnerValue($mainDiagonal)) {
            return $winner;
        }

        return null;
    }

    private function getWinnerBySideDiagonal(): ?string
    {
        $sideDiagonal = new self;
        $size = $this->getSize();

        for ($row = 1; $row <= $size; $row++) {
            $cell = $this
                ->where('row', $row)
                ->where('column', ($size - $row + 1))
                ->first();

            $sideDiagonal->push($cell);
        }

        if ($winner = $this->getWinnerValue($sideDiagonal)) {
            return $winner;
        }

        return null;
    }

    private function getWinnerValue(CellCollection $cellCollection): ?string
    {
        $value = null;
        foreach ($cellCollection as $cell) {
            if (!$cell->value) {
                return null;
            }
            $value = $cell->value;
            if ($cell->value !== $value) {
                return null;
            }
        }

        return $value;
    }
}
