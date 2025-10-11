<?php

namespace App\Generator;

use App\Entity\GDPRTreatment;
use App\Entity\Tracker;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('generator.tracker_treatment_generator')]
interface GDPRTrackerTreatmentGenerator
{
    public function generate(Tracker $tracker): GDPRTreatment;

    public function supports(Tracker $tracker): bool;
}
