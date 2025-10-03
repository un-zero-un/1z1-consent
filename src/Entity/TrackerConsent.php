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

#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class TrackerConsent implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ManyToOne(targetEntity: Consent::class, inversedBy: 'trackerConsents')]
    #[JoinColumn(nullable: false)]
    private Consent $consent;

    #[ManyToOne(targetEntity: Tracker::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Tracker $tracker;

    #[Column(type: Types::BOOLEAN, nullable: false)]
    private bool $accepted;

    public function __construct(Consent $consent, Tracker $tracker, bool $accepted)
    {
        $this->id = Uuid::v4();
        $this->consent = $consent;
        $this->tracker = $tracker;
        $this->accepted = $accepted;

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getConsent(): Consent
    {
        return $this->consent;
    }

    public function getTracker(): Tracker
    {
        return $this->tracker;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): void
    {
        $this->accepted = $accepted;
    }

    #[PrePersist]
    #[PreUpdate]
    public function preUpdate(): void
    {
        $this->getConsent()->touch();
    }
}
