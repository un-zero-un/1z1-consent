<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @api
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class TrackerConsent implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    public private(set) Uuid $id;

    #[ManyToOne(targetEntity: Consent::class, inversedBy: 'trackerConsents')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public private(set) Consent $consent;

    #[ManyToOne(targetEntity: Tracker::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public private(set) Tracker $tracker;

    #[Column(type: Types::BOOLEAN, nullable: false)]
    public bool $accepted;

    public function __construct(Consent $consent, Tracker $tracker, bool $accepted)
    {
        $this->id = Uuid::v4();
        $this->consent = $consent;
        $this->tracker = $tracker;
        $this->accepted = $accepted;

        $this->initialize();
    }

    #[PrePersist]
    #[PreUpdate]
    public function preUpdate(): void
    {
        $this->consent->touch();
    }
}
