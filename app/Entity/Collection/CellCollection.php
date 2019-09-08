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

        return $this->getWinnerValueByLine($columns);
    }

    private function getWinnerByRow(string $rowNumber): ?string
    {
        $rows = $this->where('row', $rowNumber);

        return $this->getWinnerValueByLine($rows);
    }

    private function getWinnerValueByLine(CellCollection $cellCollection): ?string
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
