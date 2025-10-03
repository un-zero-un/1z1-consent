<?php

declare(strict_types=1);

namespace App\ValueObject;

enum SecurityMeasure: string
{
    case TRACEABILITY = 'Mesures de traçabilité';
    case SOFTWARE_PROTECTION = 'Mesures de protection des logiciels';
    case BACKUP = 'Sauvegarde des données';
    case DATA_ENCRYPTION = 'Chiffrement des données';
    case ACCESS_CONTROL = 'Contrôle d\'accès des utilisateurs';
    case CONTRACTOR_CONTROL = 'Contrôle des sous-traitants';
    case OTHER = 'Autres mesures (à préciser)';
}
