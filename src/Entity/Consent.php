<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Behavior\IndirectlyHasAgency;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @api
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
#[Index(columns: ['website_id', 'user_id'])]
#[Index(fields: ['website', 'createdAt'])]
class Consent implements HasTimestamp, IndirectlyHasAgency, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ManyToOne(targetEntity: Website::class)]
    #[JoinColumn(nullable: false)]
    private Website $website;

    #[Column(type: Types::STRING, unique: true, nullable: false)]
    private string $userId;

    /**
     * @var Collection<int, TrackerConsent>
     */
    #[OneToMany(targetEntity: TrackerConsent::class, mappedBy: 'consent', cascade: ['all'])]
    private Collection $trackerConsents;

    public function __construct(Website $website, string $userId)
    {
        $this->id = Uuid::v7();
        $this->website = $website;
        $this->userId = $userId;
        $this->trackerConsents = new ArrayCollection();

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getWebsite(): Website
    {
        return $this->website;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getTrackerConsents(): Collection
    {
        return $this->trackerConsents;
    }

    public function setTrackerConsent(string $trackerId, bool $accepted): void
    {
        foreach ($this->trackerConsents as $trackerConsent) {
            if ($trackerConsent->getTracker()->getId()->toRfc4122() === $trackerId) {
                $trackerConsent->setAccepted($accepted);

                return;
            }
        }

        foreach ($this->getWebsite()->getTrackers() as $tracker) {
            if ($tracker->getId()->toRfc4122() === $trackerId) {
                $this->trackerConsents->add(new TrackerConsent($this, $tracker, $accepted));

                return;
            }
        }

        throw new \InvalidArgumentException('Tracker not found. '.$trackerId);
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->getWebsite()->getClient()?->getAgency();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->userId;
    }
}
