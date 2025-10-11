<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\IndirectlyHasAgency;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @api
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
#[Index(fields: ['website', 'createdAt'])]
class WebsiteHit implements IndirectlyHasAgency
{
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    public private(set) Uuid $id;

    #[ManyToOne(targetEntity: Website::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public private(set) Website $website;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    public private(set) \DateTimeImmutable $createdAt;

    #[Column(type: Types::STRING, nullable: true)]
    public private(set) ?string $ipAddress;

    #[Column(type: Types::STRING, nullable: false)]
    public private(set) string $referer;

    public function __construct(Website $website, string $ipAddress, string $referer)
    {
        $this->id = Uuid::v7();
        $this->website = $website;
        $this->ipAddress = $ipAddress;
        $this->referer = $referer;
        $this->createdAt = new \DateTimeImmutable();
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->website->client?->getAgency();
    }
}
