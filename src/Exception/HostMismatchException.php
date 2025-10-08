<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class HostMismatchException extends BadRequestException implements Exception
{
    public function __construct(string $expected, string $actual)
    {
        parent::__construct(sprintf('Host mismatch: expected "%s", got "%s"', $expected, $actual));
    }
}
