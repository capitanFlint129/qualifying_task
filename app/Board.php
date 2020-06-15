<?php


namespace App;


use App\Figures\Bishop;
use App\Figures\King;
use App\Figures\Knight;
use App\Figures\Pawn;
use App\Figures\Queen;
use App\Figures\Rook;

class Board
{
    private $board;
    private $kingsCoordinates;
    private $figures;

    function __construct()
    {
        $this->kingsCoordinates = [
            'white' => [mb_ord('E'), 1],
            'black' => [mb_ord('E'), 8],
        ];

        $this->board = [];
        foreach (range('A', 'H') as $vertical) {
            $this->board[$vertical] = [];
            foreach (range(1, 8) as $horizontal) {
                $this->board[$vertical][$horizontal] = null;
            }
        }

        for ($vertical = 'A'; $vertical <= 'H'; $vertical++) {
            $this->board[$vertical][2] = new Pawn('white', $this, $vertical, 2);
            $this->board[$vertical][7] = new Pawn('black', $this, $vertical, 7);
        }
        $this->board['A'][1] = new Rook('white', $this, 'A', 1);
        $this->board['A'][8] = new Rook('black', $this, 'A', 8);
        $this->board['H'][1] = new Rook('white', $this, 'H', 1);
        $this->board['H'][8] = new Rook('black', $this, 'H', 8);

        $this->board['B'][1] = new Knight('white', $this, 'B', 1);
        $this->board['B'][8] = new Knight('black', $this, 'B', 8);
        $this->board['G'][1] = new Knight('white', $this, 'G', 1);
        $this->board['G'][8] = new Knight('black', $this, 'G', 8);

        $this->board['C'][1] = new Bishop('white', $this, 'C', 1);
        $this->board['C'][8] = new Bishop('black', $this, 'C', 8);
        $this->board['F'][1] = new Bishop('white', $this, 'F', 1);
        $this->board['F'][8] = new Bishop('black', $this, 'F', 8);

        $this->board['D'][1] = new Queen('white', $this, 'D', 1);
        $this->board['D'][8] = new Queen('black', $this, 'D', 8);

        $this->board['E'][1] = new King('white', $this, 'E', 1);
        $this->board['E'][8] = new King('black', $this, 'E', 8);

        $this->figures = [
            'white' => [],
            'black' => [],
        ];

        for ($vertical = mb_ord('A'); $vertical <= mb_ord('H'); $vertical++) {
            $this->figures['white'][] = $this->board[mb_chr($vertical)][1];
            $this->figures['white'][] = $this->board[mb_chr($vertical)][2];
            $this->figures['black'][] = $this->board[mb_chr($vertical)][7];
            $this->figures['black'][] = $this->board[mb_chr($vertical)][8];
        }
    }

    public function getFigure($coordinates)
    {
        return $this->board[mb_chr($coordinates[0])][$coordinates[1]];
    }

    public function setFigure($coordinates, $figure)
    {
        $this->board[mb_chr($coordinates[0])][$coordinates[1]] = $figure;
    }

    public function kingChecks($color)
    {
        $checks = [];
        foreach ($this->figures[Game::getOppositeColor($color)] as $figure) {
            if ($figure->canReach($this->kingsCoordinates[$color])) {
                $checks[] = $figure;
            }
        }
        return $checks;
    }

    public function isCheckmate($color)
    {
        $king = $this->getFigure($this->kingsCoordinates[$color]);
        $this->setFigure($this->kingsCoordinates[$color], null);
        $checks = $this->kingChecks($color);
        if (empty($checks)) {
            $this->setFigure($this->kingsCoordinates[$color], $king);
            return false;
        }
        foreach ($king->getPossibleMoves() as $coordinates) {
            $reached = false;
            foreach ($this->figures[Game::getOppositeColor($color)] as $figure) {
                if ($figure->canReach($coordinates)) {
                    $reached = true;
                    break;
                }
            }
            if (!$reached) {
                $this->setFigure($this->kingsCoordinates[$color], $king);
                return false;
            }
        }

        if (count($checks) > 1) {
            $this->setFigure($this->kingsCoordinates[$color], $king);
            return true;
        }

        $checkFigure = array_pop($checks);

        foreach ($this->figures[$color] as $figure) {
            if ($figure->canReach($checkFigure->getCoordinates())) {
                if ($figure instanceof King) {
                    foreach ($this->figures[Game::getOppositeColor($color)] as $oppositeFigure) {
                        if ($oppositeFigure->canReach($checkFigure->getCoordinates())) {
                            $reached = true;
                            break;
                        }
                    }
                }
                if (!$reached) {
                    $this->setFigure($this->kingsCoordinates[$color], $king);
                    return false;
                }
            }
        }

        foreach ($this->getIntermediatePositions($king->getCoordinates(), $checkFigure->getCoordinates()) as $coordinates) {
            foreach ($this->figures[$color] as $figure) {
                if ($figure->canReach($coordinates)) {
                    $this->setFigure($this->kingsCoordinates[$color], $king);
                    return false;
                }
            }
        }

        $this->setFigure($this->kingsCoordinates[$color], $king);
        return true;
    }

    public function removeFigure($figure)
    {
        if (!is_null($figure)) {
            foreach ($this->figures[$figure->getColor()] as $i => $f) {
                if ($f->getCoordinates() == $figure->getCoordinates()) {
                    unset($this->figures[$figure->getColor()][$i]);
                    break;
                }
            }
        }
    }

    public static function getIntermediatePositions($firstCoordinates, $secondCoordinates)
    {
        $intermediatePositions = [];
        $minVertical = min($firstCoordinates[0], $secondCoordinates[0]);
        $minHorizontal = min($firstCoordinates[1], $secondCoordinates[1]);
        $maxVertical = max($firstCoordinates[0], $secondCoordinates[0]);
        $maxHorizontal = max($firstCoordinates[1], $secondCoordinates[1]);
        if ($minVertical == $maxVertical) {
            for ($i = $minHorizontal + 1; $i < $maxHorizontal; $i++) {
                $intermediatePositions[] = [$minVertical, $i];
            }
        } elseif ($minHorizontal == $maxHorizontal) {
            for ($i = $minVertical + 1; $i < $maxVertical; $i++) {
                $intermediatePositions[] = [$i, $minHorizontal];
            }
        } elseif ($firstCoordinates[0] - $secondCoordinates[0] == $firstCoordinates[1] - $secondCoordinates[1]) {
            for ($i = 1; $i < $maxHorizontal - $minHorizontal; $i++) {
                $intermediatePositions[] = [$minVertical + $i, $minHorizontal + $i];
            }
        } elseif ($firstCoordinates[0] - $secondCoordinates[0] == $secondCoordinates[1] - $firstCoordinates[1]) {
            for ($i = 1; $i < $maxHorizontal - $minHorizontal; $i++) {
                $intermediatePositions[] = [$maxVertical - $i, $minHorizontal + $i];
            }
        }
        return $intermediatePositions;
    }

    public function getStatusRepresentation()
    {
        $representation = [];
        $representation[] = ['0'];
        for ($vertical = 'A'; $vertical <= 'H'; $vertical++) {
            $representation[array_key_last($representation)][] = $vertical;
        }
        for ($horizontal = 1; $horizontal <= 8; $horizontal++) {
            $representation[] = [strval($horizontal)];
            for ($vertical = 'A'; $vertical <= 'H'; $vertical++) {
                if (is_null($this->getFigure([mb_ord($vertical), $horizontal]))) {
                    $representation[array_key_last($representation)][] = '__';
                } else {
                    $representation[array_key_last($representation)][] = $this->getFigure([mb_ord($vertical), $horizontal])->getAbbreviation();
                }
            }
        }
        return $representation;
    }

    public function setKingCoordinates($color, $destination)
    {
        $this->kingsCoordinates[$color] = $destination;
    }

    public function getKingCoordinates($color)
    {
        return $this->kingsCoordinates[$color];
    }
}
