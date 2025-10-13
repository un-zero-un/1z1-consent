<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\ConsentFactory;
use App\DataFixtures\Factory\WebsiteHitFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture('usage_data', ['dev'])]
final class UsageDataStory extends Story
{
    public function build(): void
    {
        ConsentFactory::createMany(250);
        WebsiteHitFactory::createMany(10_000);
    }
}
