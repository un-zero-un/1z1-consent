<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\ValueObject\PersonCategory;
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

#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class TreatmentConcernedPersonCategory implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ManyToOne(targetEntity: GDPRTreatment::class, inversedBy: 'concernedPersonCategories')]
    private ?GDPRTreatment $treatment = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false, enumType: PersonCategory::class)]
    private ?PersonCategory $personCategory = null;

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

    public function getPersonCategory(): ?PersonCategory
    {
        return $this->personCategory;
    }

    public function setPersonCategory(?PersonCategory $personCategory): void
    {
        $this->personCategory = $personCategory;
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
        return $this->personCategory?->value ?: 'A person category with non name';
    }
}
