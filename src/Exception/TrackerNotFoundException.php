<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TrackerNotFoundException extends NotFoundHttpException implements Exception
{
    public function __construct(string $uuid)
    {
        parent::__construct(sprintf('Tracker with id %s not found', $uuid));
    }
}
