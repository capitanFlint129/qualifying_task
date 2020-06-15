<?php

namespace Tests\Feature;

use App\Board;
use App\Game;
use Tests\GameTestCase;

class StatusTest extends GameTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Game::getGame()->newGame();
    }

    public function testNewGameStatus()
    {
        $response = $this->json('GET', '/status');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                "currentPlayer" => "white",
                "isOver" => false,
                "board" => (new Board())->getStatusRepresentation()
            ]);
    }

//    public function testNewGameStatus()
//    {
//        $response = $this->json('GET', '/status');
//
//        $response
//            ->assertStatus(200)
//            ->assertExactJson([
//                "currentPlayer" => "white",
//                "isOver" => false,
//                "board" => (new Board())->getBoardStatusRepresentation()
//            ]);
//    }
}
