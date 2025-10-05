<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\PersonalDataCategoryFactory;
use App\DataFixtures\Factory\SensitiveDataCategoryFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'data_categories', groups: ['main'])]
final class DataCategoryStory extends Story
{
    public function build(): void
    {
        PersonalDataCategoryFactory::createSequence([
            ['name' => 'État civil, identité, données d\'identification, images…'],
            ['name' => 'Vie personnelle (habitudes de vie, situation familiale, etc.)'],
            ['name' => 'Informations d\'ordre économique et financier (revenus, situation financière, situation fiscale, etc.)'],
            ['name' => 'Données de connexion (adresse IP, logs, etc.)'],
            ['name' => 'Données de localisation (déplacements, données GPS, GSM, etc.)'],
            ['name' => 'Numéro de Sécurité Sociale (ou NIR)'],
        ]);

        SensitiveDataCategoryFactory::createSequence([
            ['name' => 'Données révélant l\'origine raciale ou ethnique'],
            ['name' => 'Données révélant les opinions politiques'],
            ['name' => 'Données révélant les convictions religieuses ou philosophiques'],
            ['name' => 'Données révélant l\'appartenance syndicale'],
            ['name' => 'Données génétiques'],
            ['name' => 'Données biométriques aux fins d\'identifier une personne physique de manière unique'],
            ['name' => 'Données concernant la santé'],
            ['name' => 'Données concernant la vie sexuelle ou l\'orientation sexuelle'],
            ['name' => 'Données relatives à des condamnations pénales ou  infractions'],
        ]);
    }
}
