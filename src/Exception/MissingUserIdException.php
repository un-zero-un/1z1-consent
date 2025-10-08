<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class MissingUserIdException extends BadRequestException implements Exception
{
    public function __construct()
    {
        parent::__construct('Missing user_id');
    }
}
