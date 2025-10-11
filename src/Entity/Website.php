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
    public private(set) Uuid $id;

    #[NotBlank]
    #[ManyToOne(targetEntity: Client::class, fetch: 'EAGER', inversedBy: 'websites')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?Client $client = null;

    #[Column(type: Types::BOOLEAN)]
    public bool $respectDoNotTrack = true;

    #[Column(type: Types::BOOLEAN)]
    public bool $showOpenButton = true;

    #[Column(type: Types::STRING, nullable: true)]
    public ?string $dialogTitle = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $dialogText = null;

    #[Column(type: Types::TEXT, nullable: true)]
    public ?string $customCss = null;

    /**
     * @var Collection<int, WebsiteDomain>
     */
    #[Valid]
    #[OneToMany(targetEntity: WebsiteDomain::class, mappedBy: 'website', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $domains;

    #[Column(type: Types::BOOLEAN)]
    public bool $addAccessLogToGDPR = false;

    #[Column(type: Types::BOOLEAN)]
    public bool $addTrackerToGDPR = false;

    /**
     * @var Collection<int, Tracker>
     */
    #[Valid]
    #[OneToMany(targetEntity: Tracker::class, mappedBy: 'website', cascade: ['all'], orphanRemoval: true)]
    public private(set) Collection $trackers;

    #[ManyToOne(targetEntity: Server::class)]
    #[JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?Server $server = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->trackers = new ArrayCollection();
        $this->domains = new ArrayCollection();

        $this->initialize();
    }

    public function addDomain(WebsiteDomain $domain): void
    {
        $domain->website = $this;
        $this->domains->add($domain);
    }

    public function removeDomain(WebsiteDomain $domain): void
    {
        $domain->website = null;
        $this->domains->removeElement($domain);
    }

    public function addTracker(Tracker $tracker): void
    {
        $tracker->website = $this;
        $this->trackers->add($tracker);
    }

    public function removeTracker(Tracker $tracker): void
    {
        $tracker->website = null;
        $this->trackers->removeElement($tracker);
    }

    public function getGDPRTreatments(): iterable
    {
        /** @var WebsiteDomain[] $domains */
        $domains = $this->domains;

        if ($this->addAccessLogToGDPR && 0 !== count($domains)) {
            $treatment = new GDPRTreatment();
            $treatment->name = 'Journaux d\'accès';
            $treatment->ref = new AsciiSlugger()->slug($domains[0]->domain ?: '')->ascii()->lower()->toString().'-access-log';
            $treatment->client = $this->client;
            $treatment->processingPurpose = 'Journalisation des accès au site';
            $treatment->processingSubPurpose1 = 'Estimation de la charge sur le site';
            $treatment->processingSubPurpose2 = 'Détection d\'attaques et de comportements anormaux';
            $treatment->processingSubPurpose3 = 'Mise à disposition des forces de l\'ordre en cas d\'enquête';

            $category = new PersonalDataCategory();
            $category->name = 'Données de connexion (adresse IP, logs, etc.)';
            $personalDataTreatmentCategory = new PersonalDataTreatmentCategory();
            $personalDataTreatmentCategory->category = $category;
            $personalDataTreatmentCategory->description = 'Stockage du tuple Date/Heure, Adresse IP, URL demandée, User-Agent pour chaque page vue';
            $personalDataTreatmentCategory->duration = '1 an';
            $treatment->addPersonalDataCategoryTreatment($personalDataTreatmentCategory);

            $recipient = new TreatmentRecipientType();
            $recipient->recipientType = RecipientType::INTERN;
            $recipient->details = 'Personnel technique';
            $treatment->addRecipientType($recipient);

            $securityMeasure = new TreatmentSecurityMeasure();
            $securityMeasure->securityMeasure = SecurityMeasure::ACCESS_CONTROL;
            $securityMeasure->details = 'Accès restreint aux seuls personnels habilités (contrôle par clé privé)';
            $treatment->addSecurityMeasure($securityMeasure);

            yield $treatment;
        }

        if ($this->addTrackerToGDPR && 0 !== count($domains)) {
            // TODO : fake code, generate from trackers.
            $treatment = new GDPRTreatment();
            $treatment->name = 'Pisteur statistique';
            $treatment->ref = new AsciiSlugger()->slug($domains[0]->domain ?: '')->ascii()->lower()->toString().'-analytics';
            $treatment->client = $this->client;
            $treatment->processingPurpose = 'Collecte des données de fréquentation du site';
            $treatment->processingSubPurpose1 = 'Analyse de la fréquentation du site';
            $treatment->processingSubPurpose2 = 'Estimation de la charge du site';

            $category = new PersonalDataCategory();
            $category->name = 'Données de connexion (adresse IP, logs, etc.)';
            $personalDataTreatmentCategory = new PersonalDataTreatmentCategory();
            $personalDataTreatmentCategory->category = $category;
            $personalDataTreatmentCategory->description = 'Stockage des données de connexion selon configuration';
            $personalDataTreatmentCategory->duration = '2 an';
            $treatment->addPersonalDataCategoryTreatment($personalDataTreatmentCategory);

            $category2 = new PersonalDataCategory();
            $category2->name = 'Données de localisation (déplacements, données GPS, GSM, etc.)';
            $personalDataTreatmentCategory2 = new PersonalDataTreatmentCategory();
            $personalDataTreatmentCategory2->category = $category2;
            $personalDataTreatmentCategory2->description = 'Stockage des données de localisation selon configuration';
            $personalDataTreatmentCategory2->duration = '2 an';
            $treatment->addPersonalDataCategoryTreatment($personalDataTreatmentCategory2);

            $recipient = new TreatmentRecipientType();
            $recipient->recipientType = RecipientType::CONTRACTOR;
            $recipient->details = 'Google Analytics';
            $treatment->addRecipientType($recipient);

            $recipient = new TreatmentRecipientType();
            $recipient->recipientType = RecipientType::INTERN;
            $recipient->details = 'Matomo en interne';
            $treatment->addRecipientType($recipient);

            $securityMeasure = new TreatmentSecurityMeasure();
            $securityMeasure->securityMeasure = SecurityMeasure::ACCESS_CONTROL;
            $securityMeasure->details = 'Accès restreint aux seuls personnels habilités)';
            $treatment->addSecurityMeasure($securityMeasure);

            yield $treatment;
        }
    }

    #[\Override]
    public function getAgency(): ?Agency
    {
        return $this->client?->getAgency();
    }

    #[\Override]
    public function __toString(): string
    {
        return implode(
            ', ',
            $this->domains
                ->map(static fn (WebsiteDomain $domain): ?string => $domain->domain)
                ->toArray(),
        );
    }
}
