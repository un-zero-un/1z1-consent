<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class MalformedHostHeaderException extends BadRequestException implements Exception
{
    public function __construct()
    {
        parent::__construct('Malformed Host header');
    }
}
