<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidStatusTransitionException extends HttpException
{
    public function __construct(string $message = 'Invalid shipment status transition.')
    {
        parent::__construct(422, $message);
    }
}
