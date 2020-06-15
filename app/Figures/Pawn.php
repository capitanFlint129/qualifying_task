<?php


namespace App\Figures;


class Pawn extends AbstractFigure
{
    private $enPassantMove = false;

    private static $usualMoveMatrices;
    private static $captureMoveMatrices;
    private static $firstMoveMatrices;

    static public function initStatic()
    {
        self::$usualMoveMatrices['white'] = self::getEmptyMoveMatrix();
        self::$usualMoveMatrices['black'] = self::getEmptyMoveMatrix();
        self::$usualMoveMatrices['white'][7][8] = true;
        self::$usualMoveMatrices['black'][7][6] = true;

        self::$captureMoveMatrices['white'] = self::getEmptyMoveMatrix();
        self::$captureMoveMatrices['black'] = self::getEmptyMoveMatrix();
        self::$captureMoveMatrices['white'][8][8] = true;
        self::$captureMoveMatrices['white'][6][8] = true;
        self::$captureMoveMatrices['black'][6][6] = true;
        self::$captureMoveMatrices['black'][8][6] = true;

        self::$firstMoveMatrices['white'] = self::getEmptyMoveMatrix();
        self::$firstMoveMatrices['black'] = self::getEmptyMoveMatrix();
        self::$usualMoveMatrices['white'][7][9] = true;
        self::$usualMoveMatrices['black'][7][5] = true;

    }

    protected function isCorrectMove($destination)
    {
        if (is_null($this->board->getFigure($destination))) {
            if ($this->isCorrectPawnMove($destination, self::$usualMoveMatrices[$this->color])) {
                return true;
            }

            if ($this->isCorrectPawnMove($destination, self::$firstMoveMatrices[$this->color]) and !$this->moved) {
                return true;
            }

            if ($this->isCorrectPawnMove($destination, self::$captureMoveMatrices[$this->color])) {
                $takenFigureCoordinates = $destination;
                $takenFigureCoordinates[1] = $this->coordinates[1];
                $taken = $this->board->getFigure($takenFigureCoordinates);
                if ($taken instanceof Pawn and $taken->isPassant()) {
                    return true;
                }
            }
        } elseif ($this->isCorrectPawnMove($destination, self::$captureMoveMatrices[$this->color])) {
            return true;
        }

        return false;
    }


    private function isCorrectPawnMove($destination, $moveMatrix)
    {
        return $moveMatrix[$destination[0] - $this->coordinates[0] + 7][$destination[1] - $this->coordinates[1] + 7];
    }

    protected function executeMove($destination)
    {
        $taken = $this->board->getFigure($destination);
        $this->board->setFigure($destination, $this);
        $this->board->setFigure($this->coordinates, null);

        if (is_null($taken) and $destination[0] != $this->coordinates[0]) {
            $takenCoordinates = $destination;
            $takenCoordinates[1] = $this->coordinates[1];
            $taken = $this->board->getFigure($takenCoordinates);

            $this->board->setFigure($this->coordinates, null);
            $this->board->setFigure($takenCoordinates, null);
        }
        return $taken;
    }

    protected function endMove($destination, $transformationModificator = 'Q')
    {
        $this->enPassantMove = false;

        if (!$this->moved and abs($destination[1] - $this->coordinates[1]) and strcmp($destination[0], $this->coordinates[0])) {
            $this->enPassantMove = true;
        }

        if ($this->coordinates[1] == 1 or $this->coordinates[1] == 8) {
            $newFigure = new Queen($this->color, $this->board, mb_chr($this->coordinates[0]), $this->coordinates[1]);
            switch ($transformationModificator) {
                case 'Q':
                    $newFigure = new Queen($this->color, $this->board, mb_chr($this->coordinates[0]), $this->coordinates[1]);
                    break;
                case 'R':
                    $newFigure = new Rook($this->color, $this->board, mb_chr($this->coordinates[0]), $this->coordinates[1]);;
                    break;
                case 'B':
                    $newFigure = new Bishop($this->color, $this->board, mb_chr($this->coordinates[0]), $this->coordinates[1]);;
                    break;
                case 'K':
                    $newFigure = new Knight($this->color, $this->board, mb_chr($this->coordinates[0]), $this->coordinates[1]);;
                    break;
            }
            $newFigure->setMoved(true);
            $this->board->setFigure($this->coordinates, $newFigure);
            $this->board->removeFigure($this);
        }

        parent::endMove($destination);
    }

    public function isPassant()
    {
        return $this->enPassantMove;
    }

    public function getAbbreviation()
    {
        if ($this->color == 'white') {
            return 'WP';
        } else {
            return 'BP';
        }
    }
}
