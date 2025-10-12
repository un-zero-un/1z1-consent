<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Behavior\IndirectlyHasAgency;
use App\Repository\WebsiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @api
 */
#[Entity(repositoryClass: WebsiteRepository::class)]
#[Table]
#[HasLifecycleCallbacks]
class Website implements HasTimestamp, IndirectlyHasAgency, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME, unique: true)]
    public private(set) Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: Client::class, fetch: 'EAGER', inversedBy: 'websites')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Client $client = null;

    #[Column(type: Types::BOOLEAN)]
    public bool $respectDoNotTrack = true;

    #[Column(type: Types::BOOLEAN)]
    public bool $respectGlobalPrivacyControl = true;

    #[Column(type: Types::BOOLEAN)]
    public bool $showOpenButton = true;

    #[Column(type: Types::STRING, nullable: true)]
    public ?string $dialogTitle = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $dialogText = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $customCss = null;

    /**
     * @var Collection<int, WebsiteDomain>
     */
    #[Valid]
    #[OneToMany(targetEntity: WebsiteDomain::class, mappedBy: 'website', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $domains;

    #[Column(type: Types::BOOLEAN)]
    public bool $addAccessLogToGDPR = false;

    #[Column(type: Types::BOOLEAN)]
    public bool $addTrackerToGDPR = false;

    /**
     * @var Collection<int, Tracker>
     */
    #[Valid]
    #[OneToMany(targetEntity: Tracker::class, mappedBy: 'website', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $trackers;

    #[ManyToOne(targetEntity: Server::class)]
    #[JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?Server $server = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->trackers = new ArrayCollection();
        $this->domains = new ArrayCollection();

        $this->initialize();
    }

    public function addDomain(WebsiteDomain $domain): void
    {
        $domain->website = $this;
        $this->domains->add($domain);
    }

    public function removeDomain(WebsiteDomain $domain): void
    {
        $domain->website = null;
        $this->domains->removeElement($domain);
    }

    public function addTracker(Tracker $tracker): void
    {
        $tracker->website = $this;
        $this->trackers->add($tracker);
    }

    public function removeTracker(Tracker $tracker): void
    {
        $tracker->website = null;
        $this->trackers->removeElement($tracker);
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->client?->getAgency();
    }

    #[\Override]
    public function __toString(): string
    {
        return implode(
            ', ',
            $this->domains
                ->map(static fn (WebsiteDomain $domain): ?string => $domain->domain)
                ->toArray(),
        );
    }
}
