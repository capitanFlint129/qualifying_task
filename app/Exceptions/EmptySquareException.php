<?php


namespace App\Exceptions;


class EmptySquareException extends \Exception
{
    public function render($request)
    {
        return response()->json([
            'status' => 400,
            'message' => $this->getMessage(),
        ], 400);
    }
}
