<?php

namespace Tests\Feature;

use Tests\GameTestCase;

class MoveTest extends GameTestCase
{
    /**
     * @dataProvider incorrectCoordinatesProvider
     */
    public function testIncorrectCoordinates($figureCoordinates, $destination)
    {
        $response = $this->json('POST', '/make_move', [
            'figureCoordinates' => $figureCoordinates,
            'destination' => $destination,
        ]);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'figureCoordinates' => [
                    'The figure coordinates format is invalid.'
                ],
                'destination' => [
                    'The destination format is invalid.'
                ]
            ]);
    }

    public function incorrectCoordinatesProvider()
    {
        return [
            ['A0', 'A0'],
            ['A9', 'A9'],
            ['A100', 'A100'],
            ['Z1', 'K1'],
            ['Z9', 'K1'],
            ['A0', 'moveOnA3'],
        ];
    }

    public function testEmptySquare()
    {
        $response = $this->json('POST', '/make_move', [
            'figureCoordinates' => 'A3',
            'destination' => 'A4',
        ]);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'status' => 400,
                'message' => 'There is no figure on this figure coordinates.'
            ]);
    }

    public function testWrongOrder()
    {
        $response = $this->json('POST', '/make_move', [
            'figureCoordinates' => 'A7',
            'destination' => 'A6',
        ]);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'status' => 400,
                'message' => "Wrong order. Now white's move",
            ]);
    }


    /**
     * @dataProvider incorrectMoveProvider
     */
    public function testIncorrectMove($figureCoordinates, $destination)
    {
        $response = $this->json('POST', '/make_move', [
            'figureCoordinates' => $figureCoordinates,
            'destination' => $destination,
        ]);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'status' => 400,
                'message' => 'Move is incorrect'
            ]);
    }

    public function incorrectMoveProvider()
    {
        return [
            ['A2', 'A5'],
            ['B1', 'B4'],
        ];
    }

    /**
     * @dataProvider busyWayMoveProvider
     */
    public function testBusyWayMove($figureCoordinates, $destination)
    {
        $response = $this->json('POST', '/make_move', [
            'figureCoordinates' => $figureCoordinates,
            'destination' => $destination,
        ]);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'status' => 400,
                'message' => 'Another figure stands on the way'
            ]);
    }

    public function busyWayMoveProvider()
    {
        return [
            ['D1', 'D5'],
            ['F1', 'C4'],
        ];
    }

    /**
     * @dataProvider incorrectCaptureMoveProvider
     */
    public function incorrectCaptureMove($figureCoordinates, $destination)
    {
        $response = $this->json('POST', '/make_move', [
            'figureCoordinates' => $figureCoordinates,
            'destination' => $destination,
        ]);

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'status' => 400,
                'message' => 'This square is busy by your figure'
            ]);
    }

    public function incorrectCaptureMoveProvider()
    {
        return [
            ['E1', 'E2'],
            ['F1', 'C4'],
        ];
    }

    /**
     * @dataProvider correctPawnMoveProvider
     */
    public function testCorrectPawnMove($figureCoordinates, $destination, $board)
    {
        $response = $this->json('POST', '/make_move', [
            'figureCoordinates' => $figureCoordinates,
            'destination' => $destination,
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                    'isOver' => false,
                    'taken' => '',
                    'currentPlayer' => 'black',
                    'board' => $board,
                ]
            );
    }

    public function correctPawnMoveProvider()
    {
        $boards = [
            [
                ['0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
                ['1', 'WR', 'WK', 'WB', 'WQ', 'WKing', 'WB', 'WK', 'WR'],
                ['2', '__', 'WP', 'WP', 'WP', 'WP', 'WP', 'WP', 'WP'],
                ['3', 'WP', '__', '__', '__', '__', '__', '__', '__'],
                ['4', '__', '__', '__', '__', '__', '__', '__', '__'],
                ['5', '__', '__', '__', '__', '__', '__', '__', '__'],
                ['6', '__', '__', '__', '__', '__', '__', '__', '__'],
                ['7', 'BP', 'BP', 'BP', 'BP', 'BP', 'BP', 'BP', 'BP'],
                ['8', 'BR', 'BK', 'BB', 'BQ', 'BKing', 'BB', 'BK', 'BR']
            ],
            [
                ['0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
                ['1', 'WR', 'WK', 'WB', 'WQ', 'WKing', 'WB', 'WK', 'WR'],
                ['2', 'WP', 'WP', '__', 'WP', 'WP', 'WP', 'WP', 'WP'],
                ['3', '__', '__', '__', '__', '__', '__', '__', '__'],
                ['4', '__', '__', 'WP', '__', '__', '__', '__', '__'],
                ['5', '__', '__', '__', '__', '__', '__', '__', '__'],
                ['6', '__', '__', '__', '__', '__', '__', '__', '__'],
                ['7', 'BP', 'BP', 'BP', 'BP', 'BP', 'BP', 'BP', 'BP'],
                ['8', 'BR', 'BK', 'BB', 'BQ', 'BKing', 'BB', 'BK', 'BR']
            ]
        ];

        return [
            ['A2', 'A3', $boards[0]],
            ['C2', 'C4', $boards[1]],
        ];
    }

//    public function testCheckPawnMove()
//    {
//        $moveSequence = [['A2', 'A4'], ['E7', 'E5'], ['C2', 'C4'], ['F8', 'B4'], ['D2', 'D3']];
//        foreach ($moveSequence as $move) {
//            $response = $this->json('POST', '/make_move', [
//                'figureCoordinates' => $move[0],
//                'destination' => $move[1],
//            ]);
//        }
//
//        $response
//            ->assertStatus(400)
//            ->assertExactJson([
//                    'status' => 400,
//                    'message' => 'Your king is not safe'
//                ]
//            );
//    }

    public function testCheckKnightMove()
    {
        $moveSequence = [['A2', 'A4'], ['G8', 'F6'], ['E2', 'E4'], ['E7', 'E5'],
            ['E1', 'E2'], ['F6', 'E4'], ['E2', 'D3'], ['E4', 'F2'], ['D1', 'E2']];
        foreach ($moveSequence as $move) {
            $response = $this->json('POST', '/make_move', [
                'figureCoordinates' => $move[0],
                'destination' => $move[1],
            ]);
        }

        $response
            ->assertStatus(400)
            ->assertExactJson([
                    'status' => 400,
                    'message' => 'Your king is not safe'
                ]
            );
    }

    /**
     * @dataProvider capturePawnMoveProvider
     */

    public function testPawnCaptureMove($moveSequence, $taken, $board)
    {
        foreach ($moveSequence as $move) {
            $response = $this->json('POST', '/make_move', [
                'figureCoordinates' => $move[0],
                'destination' => $move[1],
            ]);
        }

        $response
            ->assertStatus(200)
            ->assertExactJson([
                    'isOver' => false,
                    'taken' => $taken,
                    'currentPlayer' => 'black',
                    'board' => $board,
                ]
            );
    }

    public function capturePawnMoveProvider()
    {
        return [
            [[['A2', 'A4'], ['B7', 'B5'], ['A4', 'B5']], 'BP',
                [
                    ['0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
                    ['1', 'WR', 'WK', 'WB', 'WQ', 'WKing', 'WB', 'WK', 'WR'],
                    ['2', '__', 'WP', 'WP', 'WP', 'WP', 'WP', 'WP', 'WP'],
                    ['3', '__', '__', '__', '__', '__', '__', '__', '__'],
                    ['4', '__', '__', '__', '__', '__', '__', '__', '__'],
                    ['5', '__', 'WP', '__', '__', '__', '__', '__', '__'],
                    ['6', '__', '__', '__', '__', '__', '__', '__', '__'],
                    ['7', 'BP', '__', 'BP', 'BP', 'BP', 'BP', 'BP', 'BP'],
                    ['8', 'BR', 'BK', 'BB', 'BQ', 'BKing', 'BB', 'BK', 'BR']
                ]
            ],
        ];
    }

//    public function testCheckmate()
//    {
//        $moveSequence = [
//            ['E2', 'E4'], ['D7', 'D5'],
//            ['E1', 'E2'], ['D5', 'E4'],
//            ['E2', 'E3'], ['E7', 'E5'],
//            ['E3', 'E4'], ['G8', 'F6'],
//            ['E4', 'E5'], ['D8', 'D6']
//        ];
//        foreach ($moveSequence as $move) {
//            $response = $this->json('POST', '/make_move', [
//                'figureCoordinates' => $move[0],
//                'destination' => $move[1],
//            ]);
//        }
//
//        $response
//            ->assertStatus(200)
//            ->assertExactJson([]);
//    }
}
