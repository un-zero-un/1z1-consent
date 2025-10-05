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
    private Uuid $id;

    #[ManyToOne(targetEntity: Agency::class)]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Agency $agency = null;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255, nullable: false)]
    private ?string $name = null;

    #[Range(min: 0)]
    #[NotNull]
    #[Column(type: Types::INTEGER, nullable: false)]
    private int $quantityOfCO2eqPerYear = 0;

    #[Range(min: 0)]
    #[NotNull]
    #[Column(type: Types::INTEGER, nullable: false)]
    private int $numberOfUnmanagedSites = 0;

    public function __construct()
    {
        $this->id = Uuid::v6();

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getQuantityOfCO2eqPerYear(): int
    {
        return $this->quantityOfCO2eqPerYear;
    }

    public function setQuantityOfCO2eqPerYear(int $quantityOfCO2eqPerYear): void
    {
        $this->quantityOfCO2eqPerYear = $quantityOfCO2eqPerYear;
    }

    public function getNumberOfUnmanagedSites(): int
    {
        return $this->numberOfUnmanagedSites;
    }

    public function setNumberOfUnmanagedSites(int $numberOfUnmanagedSites): void
    {
        $this->numberOfUnmanagedSites = $numberOfUnmanagedSites;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name ?: 'A server with no name';
    }
}
