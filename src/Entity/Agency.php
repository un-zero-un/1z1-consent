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

/**
 * @api
 */
#[Entity(repositoryClass: AgencyRepository::class)]
#[Table]
#[HasLifecycleCallbacks]
class Agency implements HasTimestamp, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[Column(type: UuidType::NAME)]
    #[GeneratedValue(strategy: 'NONE')]
    public private(set) Uuid $id;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $name = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $host = null;

    /**
     * @var Collection<int, Client>
     */
    #[OneToMany(targetEntity: Client::class, mappedBy: 'agency')]
    #[OrderBy(['name' => 'ASC'])]
    public private(set) Collection $clients;

    /**
     * @var Collection<int, AdminUser>
     */
    #[OneToMany(targetEntity: AdminUser::class, mappedBy: 'agency')]
    #[OrderBy(['email' => 'ASC'])]
    public private(set) Collection $users;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->clients = new ArrayCollection();
        $this->users = new ArrayCollection();

        $this->initialize();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name ?: 'An agency with no name';
    }
}
