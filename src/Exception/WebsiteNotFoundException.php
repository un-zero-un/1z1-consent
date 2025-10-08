<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class WebsiteNotFoundException extends NotFoundHttpException implements Exception
{
    public function __construct(array $context = [])
    {
        parent::__construct(
            sprintf(
                'Website not found (Context: %s)',
                json_encode($context),
            ),
        );
    }
}
