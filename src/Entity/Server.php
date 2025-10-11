<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasAgency;
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
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;

/**
 * @api
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class Server implements HasTimestamp, HasAgency, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    public private(set) Uuid $id;

    #[ManyToOne(targetEntity: Agency::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Agency $agency = null;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255, nullable: false)]
    public ?string $name = null;

    #[Range(min: 0)]
    #[NotNull]
    #[Column(type: Types::INTEGER, nullable: false)]
    public int $quantityOfCO2eqPerYear = 0;

    #[Range(min: 0)]
    #[NotNull]
    #[Column(type: Types::INTEGER, nullable: false)]
    public int $numberOfUnmanagedSites = 0;

    public function __construct()
    {
        $this->id = Uuid::v7();

        $this->initialize();
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    #[\Override]
    public function setAgency(?Agency $agency): void
    {
        $this->agency = $agency;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name ?: 'A server with no name';
    }
}
