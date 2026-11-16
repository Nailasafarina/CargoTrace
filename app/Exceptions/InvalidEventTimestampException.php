<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidEventTimestampException extends HttpException
{
    public function __construct(string $message = 'Invalid tracking event occurrence timestamp.')
    {
        parent::__construct(422, $message);
    }
}
