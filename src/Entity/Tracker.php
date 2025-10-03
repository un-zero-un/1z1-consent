<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\ValueObject\TrackerType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class Tracker implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: Website::class, inversedBy: 'trackers')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Website $website = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false, enumType: TrackerType::class)]
    private ?TrackerType $type = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $name = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $trackerId = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $customCode = null;

    #[Column(type: Types::STRING, length: 1024, nullable: true)]
    private ?string $customUrl = null;

    #[Column(type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    private bool $useDefaultSnippet = true;

    public function __construct()
    {
        $this->id = Uuid::v6();

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): void
    {
        $this->website = $website;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?TrackerType
    {
        return $this->type;
    }

    public function setType(?TrackerType $type): void
    {
        $this->type = $type;
    }

    public function getTrackerId(): ?string
    {
        return $this->trackerId;
    }

    public function setTrackerId(?string $trackerId): void
    {
        $this->trackerId = $trackerId;
    }

    public function getCustomCode(): ?string
    {
        return $this->customCode;
    }

    public function setCustomCode(?string $customCode): void
    {
        $this->customCode = $customCode;
    }

    public function getCustomUrl(): ?string
    {
        return $this->customUrl;
    }

    public function setCustomUrl(?string $customUrl): void
    {
        $this->customUrl = $customUrl;
    }

    public function isUseDefaultSnippet(): bool
    {
        return $this->useDefaultSnippet;
    }

    public function setUseDefaultSnippet(bool $useDefaultSnippet): void
    {
        $this->useDefaultSnippet = $useDefaultSnippet;
    }

    public function __toString(): string
    {
        return $this->getName() ?? 'A tracker with no name';
    }
}
