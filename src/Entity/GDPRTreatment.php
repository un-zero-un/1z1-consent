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
    private Uuid $id;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 255)]
    private ?string $ref = null;

    #[NotBlank]
    #[ManyToOne(targetEntity: Client::class, inversedBy: 'treatments')]
    #[JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[Column(type: Types::TEXT, nullable: false)]
    private ?string $processingPurpose = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $processingSubPurpose1 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $processingSubPurpose2 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $processingSubPurpose3 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $processingSubPurpose4 = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $processingSubPurpose5 = null;

    /**
     * @var Collection<int, PersonalDataTreatmentCategory>
     */
    #[OneToMany(mappedBy: 'treatment', targetEntity: PersonalDataTreatmentCategory::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $personalDataCategoryTreatments;

    /**
     * @var Collection<int, SensitiveDataTreatmentCategory>
     */
    #[OneToMany(mappedBy: 'treatment', targetEntity: SensitiveDataTreatmentCategory::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $sensitiveDataCategoryTreatments;

    /**
     * @var Collection<int, TreatmentConcernedPersonCategory>
     */
    #[OneToMany(mappedBy: 'treatment', targetEntity: TreatmentConcernedPersonCategory::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $concernedPersonCategories;

    /**
     * @var Collection<int, TreatmentRecipientType>
     */
    #[OneToMany(mappedBy: 'treatment', targetEntity: TreatmentRecipientType::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $recipientTypes;

    /**
     * @var Collection<int, TreatmentSecurityMeasure>
     */
    #[OneToMany(mappedBy: 'treatment', targetEntity: TreatmentSecurityMeasure::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $securityMeasures;

    /**
     * @var Collection<int, TreatmentOutOfEUTransfer>
     */
    #[OneToMany(mappedBy: 'treatment', targetEntity: TreatmentOutOfEUTransfer::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $outOfEUTransfers;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): void
    {
        $this->ref = $ref;
    }

    public function getProcessingPurpose(): ?string
    {
        return $this->processingPurpose;
    }

    public function setProcessingPurpose(?string $processingPurpose): void
    {
        $this->processingPurpose = $processingPurpose;
    }

    public function getProcessingSubPurpose1(): ?string
    {
        return $this->processingSubPurpose1;
    }

    public function setProcessingSubPurpose1(?string $processingSubPurpose1): void
    {
        $this->processingSubPurpose1 = $processingSubPurpose1;
    }

    public function getProcessingSubPurpose2(): ?string
    {
        return $this->processingSubPurpose2;
    }

    public function setProcessingSubPurpose2(?string $processingSubPurpose2): void
    {
        $this->processingSubPurpose2 = $processingSubPurpose2;
    }

    public function getProcessingSubPurpose3(): ?string
    {
        return $this->processingSubPurpose3;
    }

    public function setProcessingSubPurpose3(?string $processingSubPurpose3): void
    {
        $this->processingSubPurpose3 = $processingSubPurpose3;
    }

    public function getProcessingSubPurpose4(): ?string
    {
        return $this->processingSubPurpose4;
    }

    public function setProcessingSubPurpose4(?string $processingSubPurpose4): void
    {
        $this->processingSubPurpose4 = $processingSubPurpose4;
    }

    public function getProcessingSubPurpose5(): ?string
    {
        return $this->processingSubPurpose5;
    }

    public function setProcessingSubPurpose5(?string $processingSubPurpose5): void
    {
        $this->processingSubPurpose5 = $processingSubPurpose5;
    }

    public function getPersonalDataCategoryTreatments(): Collection
    {
        return $this->personalDataCategoryTreatments;
    }

    public function addPersonalDataCategoryTreatment(PersonalDataTreatmentCategory $personalDataTreatmentCategory): void
    {
        $personalDataTreatmentCategory->setTreatment($this);
        $this->personalDataCategoryTreatments->add($personalDataTreatmentCategory);
    }

    public function removePersonalDataCategoryTreatment(PersonalDataTreatmentCategory $personalDataTreatmentCategory): void
    {
        $personalDataTreatmentCategory->setTreatment(null);
        $this->personalDataCategoryTreatments->removeElement($personalDataTreatmentCategory);
    }

    public function getSensitiveDataCategoryTreatments(): Collection
    {
        return $this->sensitiveDataCategoryTreatments;
    }

    public function addSensitiveDataCategoryTreatment(SensitiveDataTreatmentCategory $sensitiveDataTreatmentCategory): void
    {
        $sensitiveDataTreatmentCategory->setTreatment($this);
        $this->sensitiveDataCategoryTreatments->add($sensitiveDataTreatmentCategory);
    }

    public function removeSensitiveDataCategoryTreatment(SensitiveDataTreatmentCategory $sensitiveDataTreatmentCategory): void
    {
        $sensitiveDataTreatmentCategory->setTreatment(null);
        $this->sensitiveDataCategoryTreatments->removeElement($sensitiveDataTreatmentCategory);
    }

    public function getConcernedPersonCategories(): Collection
    {
        return $this->concernedPersonCategories;
    }

    public function addConcernedPersonCategory(TreatmentConcernedPersonCategory $concernedPersonCategory): void
    {
        $concernedPersonCategory->setTreatment($this);
        $this->concernedPersonCategories->add($concernedPersonCategory);
    }

    public function removeConcernedPersonCategory(TreatmentConcernedPersonCategory $concernedPersonCategory): void
    {
        $concernedPersonCategory->setTreatment(null);
        $this->concernedPersonCategories->removeElement($concernedPersonCategory);
    }

    public function getRecipientTypes(): Collection
    {
        return $this->recipientTypes;
    }

    public function addRecipientType(TreatmentRecipientType $recipientType): void
    {
        $recipientType->setTreatment($this);
        $this->recipientTypes->add($recipientType);
    }

    public function removeRecipientType(TreatmentRecipientType $recipientType): void
    {
        $recipientType->setTreatment(null);
        $this->recipientTypes->removeElement($recipientType);
    }

    public function getSecurityMeasures(): Collection
    {
        return $this->securityMeasures;
    }

    public function addSecurityMeasure(TreatmentSecurityMeasure $securityMeasure): void
    {
        $securityMeasure->setTreatment($this);
        $this->securityMeasures->add($securityMeasure);
    }

    public function removeSecurityMeasure(TreatmentSecurityMeasure $securityMeasure): void
    {
        $securityMeasure->setTreatment(null);
        $this->securityMeasures->removeElement($securityMeasure);
    }

    public function getOutOfEUTransfers(): Collection
    {
        return $this->outOfEUTransfers;
    }

    public function addOutOfEUTransfer(TreatmentOutOfEUTransfer $outOfEUTransfer): void
    {
        $outOfEUTransfer->setTreatment($this);
        $this->outOfEUTransfers->add($outOfEUTransfer);
    }

    public function removeOutOfEUTransfer(TreatmentOutOfEUTransfer $outOfEUTransfer): void
    {
        $outOfEUTransfer->setTreatment(null);
        $this->outOfEUTransfers->removeElement($outOfEUTransfer);
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->getClient()?->getAgency();
    }
}
