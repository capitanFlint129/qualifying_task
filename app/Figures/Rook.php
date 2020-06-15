<?php


namespace App\Figures;


class Rook extends AbstractFigure
{
    static protected $moveMatrix;

    static public function initStatic()
    {
        self::$moveMatrix = self::getEmptyMoveMatrix();
        for ($i = 0; $i < 15; $i++) {
            self::$moveMatrix[$i][7] = true;
            self::$moveMatrix[7][$i] = true;
        }
        self::$moveMatrix[7][7] = false;
    }

    protected function isCorrectMove($destination)
    {
        return self::$moveMatrix[$destination[0] - $this->coordinates[0] + 7][$destination[1] - $this->coordinates[1] + 7];
    }

    public function getAbbreviation()
    {
        if ($this->color == 'white') {
            return 'WR';
        } else {
            return 'BR';
        }
    }
}
