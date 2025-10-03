<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Repository\AgencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: AgencyRepository::class)]
#[Table]
#[HasLifecycleCallbacks]
class Agency implements HasTimestamp
{
    use HasTimestampImpl;

    #[Id]
    #[Column(type: UuidType::NAME)]
    #[GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $name = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $host = null;

    /**
     * @var Collection<int, Client>
     */
    #[OneToMany(mappedBy: 'agency', targetEntity: Client::class)]
    #[OrderBy(['name' => 'ASC'])]
    private Collection $clients;

    /**
     * @var Collection<int, AdminUser>
     */
    #[OneToMany(mappedBy: 'agency', targetEntity: AdminUser::class)]
    #[OrderBy(['email' => 'ASC'])]
    private Collection $users;

    public function __construct()
    {
        $this->id = Uuid::v6();
        $this->clients = new ArrayCollection();
        $this->users = new ArrayCollection();

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function __toString(): string
    {
        return $this->getName() ?: 'An agency with no name';
    }
}
