<?php

declare(strict_types=1);

namespace App\ValueObject;

enum TrackerType: string
{
    case FACEBOOK_PIXEL = 'facebook_pixel';
    case GOOGLE_ANALYTICS = 'google_analytics';
    case GOOGLE_TAG_MANAGER = 'google_tag_manager';
    case MATOMO = 'matomo';

    case OTHER = 'other';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getLabel(): string
    {
        switch ($this) {
            case self::FACEBOOK_PIXEL:
                return 'Facebook Pixel';
            case self::GOOGLE_ANALYTICS:
                return 'Google Analytics';
            case self::GOOGLE_TAG_MANAGER:
                return 'Google Tag Manager';
            case self::MATOMO:
                return 'Matomo';
            case self::OTHER:
                return 'Autre (code sur mesure)';
        }
    }
}
