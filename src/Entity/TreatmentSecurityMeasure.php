<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\ValueObject\SecurityMeasure;
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
class TreatmentSecurityMeasure implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    public private(set) Uuid $id;

    #[ManyToOne(targetEntity: GDPRTreatment::class, inversedBy: 'securityMeasures')]
    public ?GDPRTreatment $treatment = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false, enumType: SecurityMeasure::class)]
    public ?SecurityMeasure $securityMeasure = null;

    #[Column(type: Types::STRING, nullable: true)]
    public ?string $details = null;

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
        return $this->securityMeasure?->value ?: 'A security measure with non name';
    }
}
