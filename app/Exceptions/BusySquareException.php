<?php


namespace App\Exceptions;


class BusySquareException extends \Exception
{
    public function render($request)
    {
        return response()->json([
            'status' => 400,
            'message' => $this->getMessage(),
        ], 400);
    }
}
