<?php


namespace App\Figures;


class Knight extends AbstractFigure
{
    static protected $moveMatrix;

    static public function initStatic()
    {
        self::$moveMatrix = self::getEmptyMoveMatrix();

        self::$moveMatrix[6][5] = true;
        self::$moveMatrix[5][6] = true;
        self::$moveMatrix[8][5] = true;
        self::$moveMatrix[5][8] = true;
        self::$moveMatrix[9][6] = true;
        self::$moveMatrix[6][9] = true;
        self::$moveMatrix[8][9] = true;
        self::$moveMatrix[9][8] = true;
    }

    protected function isCorrectMove($destination)
    {
        return self::$moveMatrix[$destination[0] - $this->coordinates[0] + 7][$destination[1] - $this->coordinates[1] + 7];
    }


    protected function isWayFree($destination)
    {
        return true;
    }

    public function getAbbreviation()
    {
        if ($this->color == 'white') {
            return 'WK';
        } else {
            return 'BK';
        }
    }
}
