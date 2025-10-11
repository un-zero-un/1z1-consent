<?php

namespace App\Generator;

use App\Entity\GDPRTreatment;
use App\Entity\Tracker;
use App\Entity\TreatmentRecipientType;
use App\ValueObject\RecipientType;
use App\ValueObject\TrackerType;

final readonly class MatomoTreatmentGenerator extends GenericAnalyticsTreatmentGenerator implements GDPRTrackerTreatmentGenerator
{
    #[\Override]
    public function generate(Tracker $tracker): GDPRTreatment
    {
        $treatment = $this->getBaseTreatment($tracker);

        $recipient = new TreatmentRecipientType();
        $recipient->recipientType = RecipientType::INTERN;
        if ($tracker->customUrl) {
            $recipient->details = sprintf('Matomo (sur le domaine : %s)', $tracker->customUrl);
        }
        $treatment->addRecipientType($recipient);

        return $treatment;
    }

    #[\Override]
    public function supports(Tracker $tracker): bool
    {
        return TrackerType::GOOGLE_ANALYTICS === $tracker->type;
    }
}
