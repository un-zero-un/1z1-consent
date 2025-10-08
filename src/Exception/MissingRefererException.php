<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class MissingRefererException extends BadRequestException implements Exception
{
    public function __construct()
    {
        parent::__construct('Referer header is missing');
    }
}
