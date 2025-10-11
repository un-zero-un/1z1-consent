<?php

namespace App\Generator;

use App\Entity\Client;
use App\Entity\GDPRTreatment;
use App\Entity\PersonalDataCategory;
use App\Entity\PersonalDataTreatmentCategory;
use App\Entity\TreatmentRecipientType;
use App\Entity\TreatmentSecurityMeasure;
use App\Entity\Website;
use App\ValueObject\RecipientType;
use App\ValueObject\SecurityMeasure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\String\Slugger\AsciiSlugger;

final readonly class GDPRWebsiteTreatmentGenerator
{
    public function __construct(
        /** @var iterable<GDPRTrackerTreatmentGenerator> */
        #[AutowireIterator('generator.tracker_treatment_generator')] private iterable $trackerGenerators,
    ) {
    }

    /**
     * @return iterable<GDPRTreatment>
     */
    public function generate(Website $website): iterable
    {
        if (0 === count($website->domains)) {
            return;
        }

        if ($website->addAccessLogToGDPR) {
            yield $this->generateAccessLog($website);
        }

        if (!$website->addTrackerToGDPR) {
            return;
        }

        foreach ($website->trackers as $tracker) {
            foreach ($this->trackerGenerators as $trackerGenerator) {
                if (!$trackerGenerator->supports($tracker)) {
                    continue;
                }

                yield $trackerGenerator->generate($tracker);

                break;
            }
        }
    }

    /**
     * @return iterable<GDPRTreatment>
     */
    public function generateForClient(Client $client): iterable
    {
        foreach ($client->websites as $website) {
            yield from $this->generate($website);
        }
    }

    private function generateAccessLog(Website $website): GDPRTreatment
    {
        $domains = $website->domains;

        $treatment = new GDPRTreatment();
        $treatment->name = 'Journaux d\'accès';
        $treatment->ref = new AsciiSlugger()->slug($domains[0]?->domain ?: '')->ascii()->lower()->toString().'-access-log';
        $treatment->client = $website->client;
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

        return $treatment;
    }
}
