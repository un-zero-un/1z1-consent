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
class SensitiveDataTreatmentCategory implements HasTimestamp, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    public private(set) Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: SensitiveDataCategory::class)]
    #[JoinColumn(nullable: false)]
    public ?SensitiveDataCategory $category = null;

    #[ManyToOne(targetEntity: GDPRTreatment::class, inversedBy: 'sensitiveDataCategoryTreatments')]
    #[JoinColumn(nullable: false)]
    public ?GDPRTreatment $treatment = null;

    #[NotBlank]
    #[Column(type: Types::TEXT)]
    public ?string $description = null;

    #[NotBlank]
    #[Column(type: Types::TEXT)]
    public ?string $duration = null;

    public function __construct()
    {
        $this->id = Uuid::v7();

        $this->initialize();
    }

    #[PreUpdate]
    public function preUpdate(): void
    {
        $this->treatment?->touch();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->category?->name ?: 'A sensitive data treatment with non name';
    }
}
