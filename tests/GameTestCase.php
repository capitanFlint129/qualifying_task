<?php


namespace Tests;


use App\Game;

class GameTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Game::setSerializationFile('testGameData.ser');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        Game::setSerializationFile('../gameData/game.ser');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Game::saveGame(new Game());
    }
}
