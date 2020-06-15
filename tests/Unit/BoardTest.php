<?php

namespace Tests\Unit;

use App\Board;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{

    public function testGetIntermediatePositions()
    {
        $this->assertEquals([], Board::getIntermediatePositions([mb_ord('E'), 1], [mb_ord('E'), 2]));
        $this->assertEquals([], Board::getIntermediatePositions([mb_ord('E'), 4], [mb_ord('F'), 4]));

        $this->assertEquals([[mb_ord('F'), 4]], Board::getIntermediatePositions([mb_ord('E'), 4], [mb_ord('G'), 4]));
        $this->assertEquals([[mb_ord('F'), 4], [mb_ord('G'), 4]], Board::getIntermediatePositions([mb_ord('E'), 4], [mb_ord('H'), 4]));
        $this->assertEquals([[mb_ord('F'), 4], [mb_ord('G'), 4]], Board::getIntermediatePositions([mb_ord('H'), 4], [mb_ord('E'), 4]));
        $this->assertEquals([[mb_ord('H'), 5], [mb_ord('H'), 6], [mb_ord('H'), 7]], Board::getIntermediatePositions([mb_ord('H'), 4], [mb_ord('H'), 8]));
        $this->assertEquals([[mb_ord('H'), 5], [mb_ord('H'), 6], [mb_ord('H'), 7]], Board::getIntermediatePositions([mb_ord('H'), 8], [mb_ord('H'), 4]));

        $this->assertEquals([], Board::getIntermediatePositions([mb_ord('C'), 1], [mb_ord('D'), 2]));
        $this->assertEquals([[mb_ord('D'), 2]], Board::getIntermediatePositions([mb_ord('C'), 1], [mb_ord('E'), 3]));
        $this->assertEquals([[mb_ord('D'), 2], [mb_ord('E'), 3]], Board::getIntermediatePositions([mb_ord('C'), 1], [mb_ord('F'), 4]));
        $this->assertEquals([[mb_ord('D'), 2], [mb_ord('E'), 3]], Board::getIntermediatePositions([mb_ord('F'), 4], [mb_ord('C'), 1]));

        $this->assertEquals([], Board::getIntermediatePositions([mb_ord('D'), 1], [mb_ord('C'), 2]));
        $this->assertEquals([[mb_ord('C'), 2]], Board::getIntermediatePositions([mb_ord('D'), 1], [mb_ord('B'), 3]));
        $this->assertEquals([[mb_ord('C'), 2], [mb_ord('B'), 3]], Board::getIntermediatePositions([mb_ord('D'), 1], [mb_ord('A'), 4]));
        $this->assertEquals([[mb_ord('C'), 2], [mb_ord('B'), 3]], Board::getIntermediatePositions([mb_ord('A'), 4], [mb_ord('D'), 1]));

        $this->assertEquals([], Board::getIntermediatePositions([mb_ord('A'), 1], [mb_ord('B'), 3]));
        $this->assertEquals([], Board::getIntermediatePositions([mb_ord('A'), 1], [mb_ord('B'), 8]));
        $this->assertEquals([], Board::getIntermediatePositions([mb_ord('A'), 1], [mb_ord('H'), 2]));
    }
}
