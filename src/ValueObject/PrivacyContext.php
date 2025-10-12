<?php

namespace App\ValueObject;

final readonly class PrivacyContext
{
    public function __construct(
        private(set) bool $doNotTrack,
        private(set) bool $globalPrivacyControl,
    ) {
    }
}
