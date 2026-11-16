<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidExceptionTransitionException extends HttpException
{
    public function __construct(string $message = 'Invalid shipment exception status transition.')
    {
        parent::__construct(422, $message);
    }
}
