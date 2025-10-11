<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasAgency;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @api
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class Client implements HasTimestamp, HasAgency, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME, unique: true)]
    public private(set) Uuid $id;

    #[ManyToOne(targetEntity: Agency::class, inversedBy: 'clients')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Agency $agency = null;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255, nullable: false)]
    public ?string $name = null;

    #[ManyToOne(targetEntity: Person::class, cascade: ['remove'])]
    #[JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?Person $dataResponsible = null;

    #[ManyToOne(targetEntity: Person::class, cascade: ['remove'])]
    #[JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?Person $dpo = null;

    #[OneToMany(targetEntity: GDPRTreatment::class, mappedBy: 'client')]
    public private(set) Collection $treatments;

    /**
     * @var Collection<int, Website>
     */
    #[OneToMany(targetEntity: Website::class, mappedBy: 'client')]
    public private(set) Collection $websites;

    #[Valid]
    #[OneToMany(targetEntity: Person::class, mappedBy: 'client', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $persons;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->websites = new ArrayCollection();
        $this->persons = new ArrayCollection();
        $this->treatments = new ArrayCollection();

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

    public function addPerson(Person $person): void
    {
        $person->client = $this;
        $this->persons->add($person);
    }

    public function removePerson(Person $person): void
    {
        if ($person->isEqualTo($this->dpo)) {
            $this->dpo = null;
        }
        if ($person->isEqualTo($this->dataResponsible)) {
            $this->dataResponsible = null;
        }

        $person->client = null;
        $this->persons->removeElement($person);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name ?: 'A client with no name';
    }
}
