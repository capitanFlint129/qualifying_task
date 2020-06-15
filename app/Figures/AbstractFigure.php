<?php


namespace App\Figures;


use App\Exceptions\IllegalMoveException;
use App\Game;

abstract class AbstractFigure
{
    protected $color;
    protected $board;
    protected $coordinates;
    protected $moved;

    public function __construct($color, $board, $vertical, $horizontal)
    {
        $this->moved = false;
        $this->color = $color;
        $this->board = $board;
        $this->coordinates = [mb_ord($vertical), $horizontal];
    }

    abstract protected function isCorrectMove($destination);

    abstract public function getAbbreviation();

    protected function isWayFree($destination)
    {
        $positions = $this->board->getIntermediatePositions($this->coordinates, $destination);
        foreach ($positions as $p) {
            if (!is_null($this->board->getFigure($p))) {
                return false;
            }
        }
        return true;
    }

    public function makeMove($destination, $transformationModificator = '')
    {
        if (!$this->isCorrectMove($destination)) {
            throw new IllegalMoveException('Move is incorrect');
        }

        if (!$this->isWayFree($destination)) {
            throw new IllegalMoveException('Another figure stands on the way');
        }

        if (!is_null($this->board->getFigure($destination)) and $this->color == $this->board->getFigure($destination)->getColor()) {
            throw new IllegalMoveException('This square is busy by your figure');
        }

        $taken = $this->executeMove($destination);
        $takenAbbreviation = is_null($taken) ? '' : $taken->getAbbreviation();

        if (!empty($this->board->kingChecks($this->getColor()))) {
            $this->board->setFigure($destination, null);
            if (!is_null($taken)) {
                $this->board->setFigure($taken->getCoordinates(), $taken);
            }
            $this->board->setFigure($this->coordinates, $this);
            if ($this instanceof King) {
                $this->board->setKingCoordinates($this->color, $this->coordinates);
            }
            throw new IllegalMoveException('Your king is not safe');
        }

        $response = [
            'isOver' => false,
            'taken' => $takenAbbreviation,
        ];

        if ($this->board->isCheckmate(Game::getOppositeColor($this->getColor()))) {
            $response['isOver'] = true;
            return $response;
        }

        $this->board->removeFigure($taken);
        $this->endMove($destination, $transformationModificator);
        return $response;
    }

    public function canReach($coordinates)
    {
        return ($this->isCorrectMove($coordinates) and $this->isWayFree($coordinates));
    }

    public function getColor()
    {
        return $this->color;
    }

    protected function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    protected static function getEmptyMoveMatrix()
    {
        return array_map(function () {
            return array_map(function () {
                return false;
            }, range(0, 14));
        }, range(0, 14));
    }

    protected function executeMove($destination)
    {
        $taken = $this->board->getFigure($destination);
        $this->board->setFigure($destination, $this);
        $this->board->setFigure($this->coordinates, null);
        return $taken;
    }

    protected function endMove($destination, $transformationModificator = '')
    {
        $this->moved = true;
        $this->setCoordinates($destination);
    }

    public function isMoved()
    {
        return $this->moved;
    }

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function setMoved($moved)
    {
        $this->moved = $moved;
    }
}
