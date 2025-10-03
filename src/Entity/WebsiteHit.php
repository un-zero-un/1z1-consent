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

#[Entity]
#[Table]
#[HasLifecycleCallbacks]
#[Index(fields: ['website', 'createdAt'])]
class WebsiteHit implements IndirectlyHasAgency
{
    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ManyToOne(targetEntity: Website::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Website $website;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $createdAt;

    #[Column(type: Types::STRING, nullable: false)]
    private string $ipAddress;

    #[Column(type: Types::STRING, nullable: false)]
    private string $referer;

    public function __construct(Website $website, string $ipAddress, string $referer)
    {
        $this->id = Uuid::v6();
        $this->website = $website;
        $this->ipAddress = $ipAddress;
        $this->referer = $referer;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getWebsite(): Website
    {
        return $this->website;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function getAgency(): ?Agency
    {
        return $this->getWebsite()->getClient()?->getAgency();
    }
}
