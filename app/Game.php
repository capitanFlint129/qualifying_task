<?php


namespace App;

use App\Exceptions\EmptySquareException;
use App\Exceptions\WrongOrderException;
use App\Figures\Bishop;
use App\Figures\King;
use App\Figures\Knight;
use App\Figures\Pawn;
use App\Figures\Queen;
use App\Figures\Rook;

class Game
{
    private $board;
    private $currentPlayer;
    private $over;
    private static $filePath = '../gameData/game.ser';

    public static function setSerializationFile($path)
    {
        self::$filePath = $path;
    }

    function __construct()
    {
        self::initStatic();
        $this->newGame();
    }

    private static function initStatic()
    {
        Rook::initStatic();
        Bishop::initStatic();
        Knight::initStatic();
        Queen::initStatic();
        King::initStatic();
        Pawn::initStatic();
    }

    public function newGame()
    {
        $this->currentPlayer = 'white';
        $this->over = false;
        $this->board = new Board();
    }

    public function makeMove($figureCoordinates, $destination, $transformationModificator = '')
    {
        $figureCoordinates = [mb_ord(mb_substr($figureCoordinates, 0, 1)), intval(mb_substr($figureCoordinates, 1, 1))];
        $destination = [mb_ord(mb_substr($destination, 0, 1)), intval(mb_substr($destination, 1, 1))];

        if (!$this->over) {
            $figure = $this->board->getFigure($figureCoordinates);

            if (is_null($figure)) {
                throw new EmptySquareException("There is no figure on this figure coordinates.");
            }

            if ($figure->getColor() != $this->currentPlayer) {
                throw new WrongOrderException("Wrong order. Now " . $this->currentPlayer . "'s move");
            }

            $status = $figure->makeMove($destination, $transformationModificator);
            $this->over = $status['isOver'];

            if (!$this->over) {
                $status['board'] = $this->board->getStatusRepresentation();
                $this->currentPlayer = self::getOppositeColor($this->currentPlayer);
                $status['currentPlayer'] = $this->currentPlayer;
                return $status;
            }
        }
        return [
            'message' => 'Game is over, ' . $this->currentPlayer . ' wins!'
        ];
    }

    static function getOppositeColor($color)
    {
        if ($color == 'white') {
            return 'black';
        } else {
            return 'white';
        }
    }

    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    public function isOver()
    {
        return $this->over;
    }

    public function getBoardStatusRepresentation()
    {
        return $this->board->getStatusRepresentation();
    }

    public static function getGame()
    {
        if (file_exists(self::$filePath)) {
            $game = unserialize(file_get_contents(self::$filePath));
        } else {
            $game = new Game();
        }
        self::initStatic();
        return $game;
    }

    public static function saveGame($game)
    {
        $serializedGame = serialize($game);
        $file = fopen(self::$filePath, 'w');
        fwrite($file, $serializedGame);
        fclose($file);
    }
}
