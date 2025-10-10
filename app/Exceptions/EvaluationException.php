<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class EvaluationException extends HttpException
{
    public static function make(string $message, int $code = 400): static
    {
        return new static(statusCode: $code, message: $message);
    }
}
