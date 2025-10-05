<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\ValueObject\RecipientType;
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
class TreatmentRecipientType implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ManyToOne(targetEntity: GDPRTreatment::class, inversedBy: 'recipientTypes')]
    private ?GDPRTreatment $treatment = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false, enumType: RecipientType::class)]
    private ?RecipientType $recipientType = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $details = null;

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

    public function getRecipientType(): ?RecipientType
    {
        return $this->recipientType;
    }

    public function setRecipientType(?RecipientType $recipientType): void
    {
        $this->recipientType = $recipientType;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): void
    {
        $this->details = $details;
    }

    #[PreUpdate]
    public function preUpdate(): void
    {
        $this->treatment?->touch();
    }

    public function __toString(): string
    {
        return $this->recipientType?->value ?: 'A recipient with non name';
    }
}
