<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\Equatable;
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

/**
 * @api
 *
 * @implements Equatable<self>
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class Person implements HasTimestamp, Equatable
{
    use HasTimestampImpl;

    #[Id]
    #[Column(type: UuidType::NAME)]
    #[GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ManyToOne(targetEntity: Client::class, inversedBy: 'persons')]
    #[JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $firstName = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $lastName = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $address = null;

    #[Column(type: Types::STRING, length: 7, nullable: true)]
    private ?string $postCode = null;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $city = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $country = 'FR';

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $phoneNumber = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private ?string $email = null;

    public function __construct()
    {
        $this->id = Uuid::v7();

        $this->initialize();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): void
    {
        $this->postCode = $postCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    #[\Override]
    public function isEqualTo(?object $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->id->equals($other->id);
    }

    public function __toString(): string
    {
        return sprintf('(%s) %s %s', (string) $this->client ?: '-', $this->lastName ?: 'A person', $this->firstName ?: 'with no name');
    }
}
