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
    private Uuid $id;

    #[ManyToOne(targetEntity: Agency::class, inversedBy: 'clients')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Agency $agency = null;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255, nullable: false)]
    private ?string $name = null;

    #[ManyToOne(targetEntity: Person::class)]
    #[JoinColumn(nullable: true)]
    private ?Person $dataResponsible = null;

    #[ManyToOne(targetEntity: Person::class)]
    #[JoinColumn(nullable: true)]
    private ?Person $dpo = null;

    #[OneToMany(mappedBy: 'client', targetEntity: GDPRTreatment::class)]
    private Collection $treatments;

    /**
     * @var Collection<int, Website>
     */
    #[OneToMany(mappedBy: 'client', targetEntity: Website::class)]
    private Collection $websites;

    #[Valid]
    #[OneToMany(mappedBy: 'client', targetEntity: Person::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $persons;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->websites = new ArrayCollection();
        $this->persons = new ArrayCollection();
        $this->treatments = new ArrayCollection();

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

    public function getDataResponsible(): ?Person
    {
        return $this->dataResponsible;
    }

    public function setDataResponsible(?Person $dataResponsible): void
    {
        $this->dataResponsible = $dataResponsible;
    }

    public function getDpo(): ?Person
    {
        return $this->dpo;
    }

    public function setDpo(?Person $dpo): void
    {
        $this->dpo = $dpo;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getWebsites(): Collection
    {
        return $this->websites;
    }

    public function getPersons(): Collection
    {
        return $this->persons;
    }

    public function addPerson(Person $person): void
    {
        $person->setClient($this);
        $this->persons->add($person);
    }

    public function removePerson(Person $person): void
    {
        $person->setClient(null);
        $this->persons->removeElement($person);
    }

    public function getTreatments(): Collection
    {
        return $this->treatments;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name ?: 'A client with no name';
    }
}
