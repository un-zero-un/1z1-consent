<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\ValueObject\WarrantyType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @api
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class TreatmentOutOfEUTransfer implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ManyToOne(targetEntity: GDPRTreatment::class, inversedBy: 'outOfEUTransfers')]
    private ?GDPRTreatment $treatment = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $recipient = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $country = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false, enumType: WarrantyType::class)]
    private ?WarrantyType $warrantyType = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $documentationLink = null;

    public function __construct()
    {
        $this->id = Uuid::v6();

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTreatment(): ?GDPRTreatment
    {
        return $this->treatment;
    }

    public function setTreatment(?GDPRTreatment $treatment): void
    {
        $this->treatment = $treatment;
    }

    public function getWarrantyType(): ?WarrantyType
    {
        return $this->warrantyType;
    }

    public function setWarrantyType(?WarrantyType $warrantyType): void
    {
        $this->warrantyType = $warrantyType;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getDocumentationLink(): ?string
    {
        return $this->documentationLink;
    }

    public function setDocumentationLink(?string $documentationLink): void
    {
        $this->documentationLink = $documentationLink;
    }

    #[PreUpdate]
    public function preUpdate(): void
    {
        $this->treatment?->touch();
    }

    public function __toString(): string
    {
        return $this->country ?: 'A country with non name';
    }
}
