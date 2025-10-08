<?php

namespace App\Exception;

final class UserHasNoAgencyException extends \RuntimeException implements Exception
{
    public function __construct()
    {
        parent::__construct('The user has no agency assigned');
    }
}
