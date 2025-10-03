<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\PersonalDataCategory;
use App\Entity\SensitiveDataCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DataCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (
            [
                'État civil, identité, données d\'identification, images…',
                'Vie personnelle (habitudes de vie, situation familiale, etc.)',
                'Informations d\'ordre économique et financier (revenus, situation financière, situation fiscale, etc.)',
                'Données de connexion (adresse IP, logs, etc.)',
                'Données de localisation (déplacements, données GPS, GSM, etc.)',
                'Numéro de Sécurité Sociale (ou NIR)',
            ] as $item
        ) {
            $personalDataCategory = new PersonalDataCategory();
            $personalDataCategory->setName($item);

            $manager->persist($personalDataCategory);
        }

        foreach (
            [
                'Données révélant l\'origine raciale ou ethnique',
                'Données révélant les opinions politiques',
                'Données révélant les convictions religieuses ou philosophiques',
                'Données révélant l\'appartenance syndicale',
                'Données génétiques',
                'Données biométriques aux fins d\'identifier une personne physique de manière unique',
                'Données concernant la santé',
                'Données concernant la vie sexuelle ou l\'orientation sexuelle',
                'Données relatives à des condamnations pénales ou  infractions',
            ] as $item
        ) {
            $sensitiveDataCategory = new SensitiveDataCategory();
            $sensitiveDataCategory->setName($item);

            $manager->persist($sensitiveDataCategory);
        }

        $manager->flush();
    }
}
