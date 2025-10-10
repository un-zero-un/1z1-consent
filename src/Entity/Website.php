<?php

declare(strict_types=1);

namespace App\Entity;

use App\Behavior\HasTimestamp;
use App\Behavior\Impl\HasTimestampImpl;
use App\Behavior\IndirectlyHasAgency;
use App\Repository\WebsiteRepository;
use App\ValueObject\RecipientType;
use App\ValueObject\SecurityMeasure;
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
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @api
 */
#[Entity(repositoryClass: WebsiteRepository::class)]
#[Table]
#[HasLifecycleCallbacks]
class Website implements HasTimestamp, IndirectlyHasAgency, \Stringable
{
    use HasTimestampImpl;

    #[Id]
    #[GeneratedValue(strategy: 'NONE')]
    #[Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: Client::class, fetch: 'EAGER', inversedBy: 'websites')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Client $client = null;

    #[Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $respectDoNotTrack = true;

    #[Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $showOpenButton = true;

    #[Column(type: Types::STRING, nullable: true)]
    private ?string $dialogTitle = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $dialogText = null;

    #[Column(type: Types::TEXT, nullable: true)]
    private ?string $customCss = null;

    /**
     * @var Collection<int, WebsiteDomain>
     */
    #[Valid]
    #[OneToMany(targetEntity: WebsiteDomain::class, mappedBy: 'website', cascade: ['all'], orphanRemoval: true)]
    private Collection $domains;

    #[Column(type: Types::BOOLEAN, options: ['default' => 'FALSE'])]
    private bool $addAccessLogToGDPR = false;

    #[Column(type: Types::BOOLEAN, options: ['default' => 'FALSE'])]
    private bool $addTrackerToGDPR = false;

    /**
     * @var Collection<int, Tracker>
     */
    #[Valid]
    #[OneToMany(mappedBy: 'website', targetEntity: Tracker::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $trackers;

    #[ManyToOne(targetEntity: Server::class)]
    #[JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Server $server = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->trackers = new ArrayCollection();
        $this->domains = new ArrayCollection();

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

    public function isRespectDoNotTrack(): bool
    {
        return $this->respectDoNotTrack;
    }

    public function setRespectDoNotTrack(bool $respectDoNotTrack): void
    {
        $this->respectDoNotTrack = $respectDoNotTrack;
    }

    public function isShowOpenButton(): bool
    {
        return $this->showOpenButton;
    }

    public function setShowOpenButton(bool $showOpenButton): void
    {
        $this->showOpenButton = $showOpenButton;
    }

    public function getDialogTitle(): ?string
    {
        return $this->dialogTitle;
    }

    public function setDialogTitle(?string $dialogTitle): void
    {
        $this->dialogTitle = $dialogTitle;
    }

    public function getDialogText(): ?string
    {
        return $this->dialogText;
    }

    public function setDialogText(?string $dialogText): void
    {
        $this->dialogText = $dialogText;
    }

    public function getCustomCss(): ?string
    {
        return $this->customCss;
    }

    public function setCustomCss(?string $customCss): void
    {
        $this->customCss = $customCss;
    }

    /**
     * @return Collection<int, WebsiteDomain>
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    public function addDomain(WebsiteDomain $domain): void
    {
        $domain->setWebsite($this);
        $this->domains->add($domain);
    }

    public function removeDomain(WebsiteDomain $domain): void
    {
        $domain->setWebsite(null);
        $this->domains->removeElement($domain);
    }

    /**
     * @return Collection<int, Tracker>
     */
    public function getTrackers(): Collection
    {
        return $this->trackers;
    }

    public function addTracker(Tracker $tracker): void
    {
        $tracker->setWebsite($this);
        $this->trackers->add($tracker);
    }

    public function removeTracker(Tracker $tracker): void
    {
        $tracker->setWebsite(null);
        $this->trackers->removeElement($tracker);
    }

    public function isAddAccessLogToGDPR(): bool
    {
        return $this->addAccessLogToGDPR;
    }

    public function setAddAccessLogToGDPR(bool $addAccessLogToGDPR): void
    {
        $this->addAccessLogToGDPR = $addAccessLogToGDPR;
    }

    public function isAddTrackerToGDPR(): bool
    {
        return $this->addTrackerToGDPR;
    }

    public function setAddTrackerToGDPR(bool $addTrackerToGDPR): void
    {
        $this->addTrackerToGDPR = $addTrackerToGDPR;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(?Server $server): void
    {
        $this->server = $server;
    }

    public function getGDPRTreatments(): iterable
    {
        /** @var WebsiteDomain[] $domains */
        $domains = $this->getDomains();

        if ($this->isAddAccessLogToGDPR() && 0 !== count($domains)) {
            $treatment = new GDPRTreatment();
            $treatment->setName('Journaux d\'accès');
            $treatment->setRef((new AsciiSlugger())->slug($domains[0]->getDomain() ?: '')->ascii()->lower()->toString().'-access-log');
            $treatment->setClient($this->getClient());
            $treatment->setProcessingPurpose('Journalisation des accès au site');
            $treatment->setProcessingSubPurpose1('Estimation de la charge sur le site');
            $treatment->setProcessingSubPurpose2('Détection d\'attaques et de comportements anormaux');
            $treatment->setProcessingSubPurpose3('Mise à disposition des forces de l\'ordre en cas d\'enquête');

            $category = new PersonalDataCategory();
            $category->setName('Données de connexion (adresse IP, logs, etc.)');
            $personalDataTreatmentCategory = new PersonalDataTreatmentCategory();
            $personalDataTreatmentCategory->setCategory($category);
            $personalDataTreatmentCategory->setDescription('Stockage du tuple Date/Heure, Adresse IP, URL demandée, User-Agent pour chaque page vue');
            $personalDataTreatmentCategory->setDuration('1 an');
            $treatment->addPersonalDataCategoryTreatment($personalDataTreatmentCategory);

            $recipient = new TreatmentRecipientType();
            $recipient->setRecipientType(RecipientType::INTERN);
            $recipient->setDetails('Personnel technique');
            $treatment->addRecipientType($recipient);

            $securityMeasure = new TreatmentSecurityMeasure();
            $securityMeasure->setSecurityMeasure(SecurityMeasure::ACCESS_CONTROL);
            $securityMeasure->setDetails('Accès restreint aux seuls personnels habilités (contrôle par clé privé)');
            $treatment->addSecurityMeasure($securityMeasure);

            yield $treatment;
        }

        if ($this->isAddTrackerToGDPR() && 0 !== count($domains)) {
            // TODO : fake code, generate from trackers.
            $treatment = new GDPRTreatment();
            $treatment->setName('Pisteur statistique');
            $treatment->setRef(new AsciiSlugger()->slug($domains[0]->getDomain() ?: '')->ascii()->lower()->toString().'-analytics');
            $treatment->setClient($this->getClient());
            $treatment->setProcessingPurpose('Collecte des données de fréquentation du site');
            $treatment->setProcessingSubPurpose1('Analyse de la fréquentation du site');
            $treatment->setProcessingSubPurpose2('Estimation de la charge du site');

            $category = new PersonalDataCategory();
            $category->setName('Données de connexion (adresse IP, logs, etc.)');
            $personalDataTreatmentCategory = new PersonalDataTreatmentCategory();
            $personalDataTreatmentCategory->setCategory($category);
            $personalDataTreatmentCategory->setDescription('Stockage des données de connexion selon configuration');
            $personalDataTreatmentCategory->setDuration('2 an');
            $treatment->addPersonalDataCategoryTreatment($personalDataTreatmentCategory);

            $category2 = new PersonalDataCategory();
            $category2->setName('Données de localisation (déplacements, données GPS, GSM, etc.)');
            $personalDataTreatmentCategory2 = new PersonalDataTreatmentCategory();
            $personalDataTreatmentCategory2->setCategory($category2);
            $personalDataTreatmentCategory2->setDescription('Stockage des données de localisation selon configuration');
            $personalDataTreatmentCategory2->setDuration('2 an');
            $treatment->addPersonalDataCategoryTreatment($personalDataTreatmentCategory2);

            $recipient = new TreatmentRecipientType();
            $recipient->setRecipientType(RecipientType::CONTRACTOR);
            $recipient->setDetails('Google Analytics');
            $treatment->addRecipientType($recipient);

            $recipient = new TreatmentRecipientType();
            $recipient->setRecipientType(RecipientType::INTERN);
            $recipient->setDetails('Matomo en interne');
            $treatment->addRecipientType($recipient);

            $securityMeasure = new TreatmentSecurityMeasure();
            $securityMeasure->setSecurityMeasure(SecurityMeasure::ACCESS_CONTROL);
            $securityMeasure->setDetails('Accès restreint aux seuls personnels habilités)');
            $treatment->addSecurityMeasure($securityMeasure);

            yield $treatment;
        }
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->getClient()?->getAgency();
    }

    #[\Override]
    public function __toString(): string
    {
        return implode(
            ', ',
            $this->getDomains()
                ->map(static fn (WebsiteDomain $domain): ?string => $domain->getDomain())
                ->toArray(),
        );
    }
}
