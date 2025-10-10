<?php

namespace App\Exception;

use App\Entity\AdminUser;

final class InvalidUserException extends \RuntimeException implements Exception
{
    public function __construct(mixed $userOrNull)
    {
        $type = is_object($userOrNull) ? $userOrNull::class : gettype($userOrNull);

        parent::__construct(sprintf('Invalid user, expected instance of %s, got %s', AdminUser::class, $type));
    }
}
