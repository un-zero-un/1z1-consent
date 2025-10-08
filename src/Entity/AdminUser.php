<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasAgency;
use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Exception\MissingEmailException;
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
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @api
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class AdminUser implements HasTimestamp, UserInterface, PasswordAuthenticatedUserInterface, HasAgency, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ManyToOne(targetEntity: Agency::class, inversedBy: 'users')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Agency $agency = null;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 180, unique: true, nullable: false)]
    private ?string $email;

    #[Column(type: Types::JSON)]
    private array $roles = [];

    #[Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $googleId;

    #[Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $password = null;

    public function __construct(?string $email = null, ?string $googleId = null)
    {
        $this->id = Uuid::v6();
        $this->email = $email;
        $this->googleId = $googleId;

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

    #[\Override]
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    #[\Override]
    public function eraseCredentials(): void
    {
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        if (!$this->email) {
            throw new MissingEmailException($this);
        }

        return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    #[\Override]
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): void
    {
        $this->googleId = $googleId;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getUserIdentifier() ?: 'An user with no email';
    }
}
