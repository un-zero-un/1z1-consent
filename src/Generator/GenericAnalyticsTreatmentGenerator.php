<?php

namespace App\Generator;

use App\Entity\GDPRTreatment;
use App\Entity\PersonalDataCategory;
use App\Entity\PersonalDataTreatmentCategory;
use App\Entity\Tracker;
use App\Entity\TreatmentSecurityMeasure;
use App\ValueObject\SecurityMeasure;
use Symfony\Component\String\Slugger\AsciiSlugger;

abstract readonly class GenericAnalyticsTreatmentGenerator implements GDPRTrackerTreatmentGenerator
{
    final protected function getBaseTreatment(Tracker $tracker): GDPRTreatment
    {
        assert(null !== $tracker->website);

        $domains = $tracker->website->domains;
        assert(count($domains) > 0);

        $treatment = new GDPRTreatment();
        $treatment->name = 'Pisteur statistique';
        $treatment->ref = new AsciiSlugger()->slug($domains[0]?->domain ?: '')->ascii()->lower()->toString().'-analytics';
        $treatment->client = $tracker->website->client;
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

        $securityMeasure = new TreatmentSecurityMeasure();
        $securityMeasure->securityMeasure = SecurityMeasure::ACCESS_CONTROL;
        $securityMeasure->details = 'Accès restreint aux seuls personnels habilités)';
        $treatment->addSecurityMeasure($securityMeasure);

        return $treatment;
    }
}
