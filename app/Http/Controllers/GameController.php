<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    public function status()
    {
        $game = Game::getGame();
        $response = [
            'currentPlayer' => $game->getCurrentPlayer(),
            'isOver' => $game->isOver(),
            'board' => $game->getBoardStatusRepresentation(),
        ];
        if ($game->isOver()) {
            $response['winner'] = $game->getCurrentPlayer();
        }
        Game::saveGame($game);
        return response()->json($response);
    }

    public function makeMove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'figureCoordinates' => ['required', 'regex:/\b[A-Ha-h][1-8]\b/'],
            'destination' => ['required', 'regex:/\b[A-Ha-h][1-8]\b/'],
            'transformationModificator' => ['regex:/\bR|Q|K|B\b/']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $figureCoordinates = strtoupper($request->input('figureCoordinates'));
        $destination = strtoupper($request->input('destination'));
        $transformationModificator = $request->input('transformationModificator', '');

        $game = Game::getGame();
        $response = $game->makeMove($figureCoordinates, $destination, $transformationModificator);
        Game::saveGame($game);
        return response()->json($response, 200);
    }

    public function newGame()
    {
        Game::saveGame(new Game());
        return response('', 204);
    }
}
