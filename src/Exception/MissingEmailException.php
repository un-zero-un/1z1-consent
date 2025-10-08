<?php

namespace App\Exception;

class MissingEmailException extends \RuntimeException implements Exception
{
    public function __construct(object $object)
    {
        parent::__construct(sprintf('Missing email in class %s', get_class($object)));
    }
}
