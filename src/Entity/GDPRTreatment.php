<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Behavior\IndirectlyHasAgency;
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
class GDPRTreatment implements HasTimestamp, IndirectlyHasAgency
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME)]
    public private(set) Uuid $id;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255)]
    public ?string $name = null;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255)]
    public ?string $ref = null;

    #[NotBlank]
    #[ManyToOne(targetEntity: Client::class, inversedBy: 'treatments')]
    #[JoinColumn(nullable: false)]
    public ?Client $client = null;

    #[Column(type: Types::TEXT, nullable: false)]
    public ?string $processingPurpose = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $processingSubPurpose1 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $processingSubPurpose2 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $processingSubPurpose3 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $processingSubPurpose4 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $processingSubPurpose5 = null;

    /**
     * @var Collection<int, PersonalDataTreatmentCategory>
     */
    #[Valid]
    #[OneToMany(targetEntity: PersonalDataTreatmentCategory::class, mappedBy: 'treatment', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $personalDataCategoryTreatments;

    /**
     * @var Collection<int, SensitiveDataTreatmentCategory>
     */
    #[Valid]
    #[OneToMany(targetEntity: SensitiveDataTreatmentCategory::class, mappedBy: 'treatment', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $sensitiveDataCategoryTreatments;

    /**
     * @var Collection<int, TreatmentConcernedPersonCategory>
     */
    #[Valid]
    #[OneToMany(targetEntity: TreatmentConcernedPersonCategory::class, mappedBy: 'treatment', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $concernedPersonCategories;

    /**
     * @var Collection<int, TreatmentRecipientType>
     */
    #[Valid]
    #[OneToMany(targetEntity: TreatmentRecipientType::class, mappedBy: 'treatment', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $recipientTypes;

    /**
     * @var Collection<int, TreatmentSecurityMeasure>
     */
    #[Valid]
    #[OneToMany(targetEntity: TreatmentSecurityMeasure::class, mappedBy: 'treatment', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $securityMeasures;

    /**
     * @var Collection<int, TreatmentOutOfEUTransfer>
     */
    #[OneToMany(targetEntity: TreatmentOutOfEUTransfer::class, mappedBy: 'treatment', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $outOfEUTransfers;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->personalDataCategoryTreatments = new ArrayCollection();
        $this->sensitiveDataCategoryTreatments = new ArrayCollection();
        $this->concernedPersonCategories = new ArrayCollection();
        $this->recipientTypes = new ArrayCollection();
        $this->securityMeasures = new ArrayCollection();
        $this->outOfEUTransfers = new ArrayCollection();

        $this->initialize();
    }

    public function addPersonalDataCategoryTreatment(PersonalDataTreatmentCategory $personalDataTreatmentCategory): void
    {
        $personalDataTreatmentCategory->treatment = $this;
        $this->personalDataCategoryTreatments->add($personalDataTreatmentCategory);
    }

    public function removePersonalDataCategoryTreatment(PersonalDataTreatmentCategory $personalDataTreatmentCategory): void
    {
        $personalDataTreatmentCategory->treatment = null;
        $this->personalDataCategoryTreatments->removeElement($personalDataTreatmentCategory);
    }

    public function addSensitiveDataCategoryTreatment(SensitiveDataTreatmentCategory $sensitiveDataTreatmentCategory): void
    {
        $sensitiveDataTreatmentCategory->treatment = $this;
        $this->sensitiveDataCategoryTreatments->add($sensitiveDataTreatmentCategory);
    }

    public function removeSensitiveDataCategoryTreatment(SensitiveDataTreatmentCategory $sensitiveDataTreatmentCategory): void
    {
        $sensitiveDataTreatmentCategory->treatment = null;
        $this->sensitiveDataCategoryTreatments->removeElement($sensitiveDataTreatmentCategory);
    }

    public function addConcernedPersonCategory(TreatmentConcernedPersonCategory $concernedPersonCategory): void
    {
        $concernedPersonCategory->treatment = $this;
        $this->concernedPersonCategories->add($concernedPersonCategory);
    }

    public function removeConcernedPersonCategory(TreatmentConcernedPersonCategory $concernedPersonCategory): void
    {
        $concernedPersonCategory->treatment = null;
        $this->concernedPersonCategories->removeElement($concernedPersonCategory);
    }

    public function addRecipientType(TreatmentRecipientType $recipientType): void
    {
        $recipientType->treatment = $this;
        $this->recipientTypes->add($recipientType);
    }

    public function removeRecipientType(TreatmentRecipientType $recipientType): void
    {
        $recipientType->treatment = null;
        $this->recipientTypes->removeElement($recipientType);
    }

    public function addSecurityMeasure(TreatmentSecurityMeasure $securityMeasure): void
    {
        $securityMeasure->treatment = $this;
        $this->securityMeasures->add($securityMeasure);
    }

    public function removeSecurityMeasure(TreatmentSecurityMeasure $securityMeasure): void
    {
        $securityMeasure->treatment = null;
        $this->securityMeasures->removeElement($securityMeasure);
    }

    public function addOutOfEUTransfer(TreatmentOutOfEUTransfer $outOfEUTransfer): void
    {
        $outOfEUTransfer->treatment = $this;
        $this->outOfEUTransfers->add($outOfEUTransfer);
    }

    public function removeOutOfEUTransfer(TreatmentOutOfEUTransfer $outOfEUTransfer): void
    {
        $outOfEUTransfer->treatment = null;
        $this->outOfEUTransfers->removeElement($outOfEUTransfer);
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->client?->getAgency();
    }
}
