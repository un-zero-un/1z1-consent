<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
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
#[Index(fields: ['domain'])]
#[Table]
#[HasLifecycleCallbacks]
class WebsiteDomain implements HasTimestamp, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME, nullable: false)]
    public private(set) Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: Website::class, inversedBy: 'domains')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Website $website = null;

    #[NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    public ?string $domain = null;

    public function __construct()
    {
        $this->id = Uuid::v7();

        $this->initialize();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->domain ?? 'A domain with no name';
    }
}
