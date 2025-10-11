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
class Person implements HasTimestamp, Equatable, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[Column(type: UuidType::NAME)]
    #[GeneratedValue(strategy: 'NONE')]
    public private(set) Uuid $id;

    #[ManyToOne(targetEntity: Client::class, inversedBy: 'persons')]
    #[JoinColumn(nullable: false)]
    public ?Client $client = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $firstName = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $lastName = null;

    #[Column(type: Types::STRING, nullable: true)]
    public ?string $address = null;

    #[Column(type: Types::STRING, length: 7, nullable: true)]
    public ?string $postCode = null;

    #[Column(type: Types::STRING, nullable: true)]
    public ?string $city = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $country = 'FR';

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $phoneNumber = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $email = null;

    public function __construct()
    {
        $this->id = Uuid::v7();

        $this->initialize();
    }

    #[\Override]
    public function isEqualTo(?object $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->id->equals($other->id);
    }

    #[\Override]
    public function __toString(): string
    {
        return sprintf('(%s) %s %s', (string) $this->client ?: '-', $this->lastName ?: 'A person', $this->firstName ?: 'with no name');
    }
}
