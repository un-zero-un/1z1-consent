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
class PersonalDataTreatmentCategory implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    private Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: PersonalDataCategory::class)]
    #[JoinColumn(nullable: false)]
    private ?PersonalDataCategory $category = null;

    #[ManyToOne(targetEntity: GDPRTreatment::class, inversedBy: 'personalDataCategoryTreatments')]
    #[JoinColumn(nullable: false)]
    private ?GDPRTreatment $treatment = null;

    #[NotBlank]
    #[Column(type: Types::TEXT)]
    private ?string $description = null;

    #[NotBlank]
    #[Column(type: Types::TEXT)]
    private ?string $duration = null;

    public function __construct()
    {
        $this->id = Uuid::v7();

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCategory(): ?PersonalDataCategory
    {
        return $this->category;
    }

    public function setCategory(?PersonalDataCategory $category): void
    {
        $this->category = $category;
    }

    public function getTreatment(): ?GDPRTreatment
    {
        return $this->treatment;
    }

    public function setTreatment(?GDPRTreatment $treatment): void
    {
        $this->treatment = $treatment;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): void
    {
        $this->duration = $duration;
    }

    #[PreUpdate]
    public function preUpdate(): void
    {
        $this->treatment?->touch();
    }

    public function __toString(): string
    {
        return $this->category?->getName() ?: 'A personal data treatment with non name';
    }
}
