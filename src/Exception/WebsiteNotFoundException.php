<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class WebsiteNotFoundException extends NotFoundHttpException implements Exception
{
    public function __construct(array $context = [])
    {
        $serializedContext = json_encode($context);
        assert(false !== $serializedContext);

        parent::__construct(
            sprintf(
                'Website not found (Context: %s)',
                $serializedContext,
            ),
        );
    }
}
