<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\ValueObject\TrackerType;
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
 */
#[Entity]
#[Table]
#[HasLifecycleCallbacks]
class Tracker implements HasTimestamp, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME, unique: true)]
    public private(set) Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: Website::class, inversedBy: 'trackers')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Website $website = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false, enumType: TrackerType::class)]
    public ?TrackerType $type = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $name = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $trackerId = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $customCode = null;

    #[Column(type: Types::STRING, length: 1024, nullable: true)]
    public ?string $customUrl = null;

    #[Column(type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    public bool $useDefaultSnippet = true;

    public function __construct()
    {
        $this->id = Uuid::v7();

        $this->initialize();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name ?? 'A tracker with no name';
    }
}
