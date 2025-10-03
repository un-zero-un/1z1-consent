<?php

declare(strict_types=1);

namespace App\ValueObject;

enum WarrantyType: string
{
    case STANDARD_CONTRACTUAL_CLAUSES = 'Clauses contractuelles types (CCT)';
    case RESTRICTIVE_COMPANY_RULE = 'Règles d\'entreprise contraignantes (BCR)';
    case SUITABLE_COUNTRY = 'Pays adéquat';
    case PRIVACY_SHIELD = 'Privacy shield';
    case CODE_OF_CONDUCT = 'Code de conduite';
    case CERTIFICATION = 'Certification';
    case EXEMPTION = 'Dérogations (art 49)';
}
