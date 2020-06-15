<?php


namespace App\Figures;


use App\Game;

class King extends AbstractFigure
{
    static protected $usualMoveMatrix;
    static protected $castlingMatrix;

    static public function initStatic()
    {
        self::$usualMoveMatrix = self::getEmptyMoveMatrix();
        for ($i = 6; $i <= 8; $i++) {
            for ($j = 6; $j <= 8; $j++) {
                self::$usualMoveMatrix[$i][$j] = true;
            }
        }
        self::$usualMoveMatrix[7][7] = false;

        self::$castlingMatrix = self::getEmptyMoveMatrix();
        self::$castlingMatrix[5][7] = true;
        self::$castlingMatrix[9][7] = true;
    }

    public function getPossibleMoves()
    {
        $possibleMoves = [];
        for ($i = -1; $i < 2; $i++) {
            for ($j = -1; $j < 2; $j++) {
                if (($i != 0 or $j != 0) and $this->isPossibleMove($this->coordinates[0] + $i, $this->coordinates[1] + $j)) {
                    $possibleMoves[] = [$this->coordinates[0] + $i, ($this->coordinates[1] + $j)];
                }
            }
        }
        return $possibleMoves;
    }

    private function isPossibleMove($vertical, $horizontal)
    {
        if ($vertical < mb_ord('A') or $vertical > mb_ord('H') or $horizontal < 1 or $horizontal > 8) {
            return false;
        }
        $figure = $this->board->getFigure([$vertical, $horizontal]);
        if (!is_null($figure) and $figure->getColor() == $this->color) {
            return false;
        }
        return true;
    }

    protected function isCorrectMove($destination)
    {
        if ($this->isCorrectKingMove($destination, self::$usualMoveMatrix)) {
            return true;
        }

        if ($this->isCorrectKingMove($destination, self::$castlingMatrix)) {
            if ($destination[0] > $this->coordinates[0]) {
                $rook = $this->board->getFigure('H' . $this->coordinates[1]);
            } else {
                $rook = $this->board->getFigure('A' . $this->coordinates[1]);
            }

            if (is_null($rook) or !($rook instanceof Rook)) {
                return false;
            }

            if (!$this->moved and !$rook->isMoved()) {
                return false;
            }

            foreach ($this->board->getIntermediatePositions($rook, $this) as $position) {
                foreach ($this->board->figures[Game::getOppositeColor($this->color)] as $figure) {
                    if ($figure->canReach($position)) {
                        return false;
                    }
                }
            }

            if (!empty($this->board->kingChecks[$this->color])) return false;

            if ($destination[0] > $this->coordinates[0]) {
                $newRookCoordinates = (($destination[0] - 1) . $destination[1]);
            } else {
                $newRookCoordinates = (($destination[0] + 1) . $destination[1]);
            }

            $this->executeCastling($rook, $destination);

            if (!empty($this->board->kingChecks[$this->color])) {
                $this->board->setFigure($this->coordinates, $this);
                $this->board->setFigure($rook->getCoordinates(), $rook);
                $this->board->setFigure($destination, null);
                $this->board->setFigure($newRookCoordinates, null);
                return false;
            }
            return true;
        }

        return false;
    }

    private function isCorrectKingMove($destination, $moveMatrix)
    {
        return $moveMatrix[$destination[0] - $this->coordinates[0] + 7][$destination[1] - $this->coordinates[1] + 7];
    }

    protected function executeMove($destination)
    {
        $this->board->setKingCoordinates($this->color, $destination);
        if (abs($destination[0] - $this->coordinates[0]) < 2) {
            $taken = $this->board->getFigure($destination);
            $this->board->setFigure($destination, $this);
            $this->board->setFigure($this->coordinates, null);
            return $taken;
        } else {
            if ($destination[0] > $this->coordinates[0]) {
                $rook = $this->board->getFigure('H' . $this->coordinates[1]);
            } else {
                $rook = $this->board->getFigure('A' . $this->coordinates[1]);
            }
            $this->executeCastling($rook, $destination);
            return null;
        }
    }

    private function executeCastling($rook, $destination)
    {
        if ($destination[0] > $this->coordinates[0]) {
            $newRookCoordinates = (($destination[0] - 1) . $destination[1]);
        } else {
            $newRookCoordinates = (($destination[0] + 1) . $destination[1]);
        }

        $this->board->setFigure($destination[0] . $destination[1], $this);
        $this->board->setFigure($this->coordinates, null);
        $this->board->setFigure($newRookCoordinates, $rook);
        $this->board->setFigure($rook->getCoordinates(), null);
    }

    protected function endMove($destination, $transformationModificator = '')
    {
        parent::endMove($destination);
    }

    public function getAbbreviation()
    {
        if ($this->color == 'white') {
            return 'WKing';
        } else {
            return 'BKing';
        }
    }
}
