<?php

declare(strict_types=1);

namespace App\ValueObject;

enum RecipientType: string
{
    case INTERN = 'Service interne qui traite les données';
    case CONTRACTOR = 'Sous-traitants';
    case INTERNATIONAL = 'Destinataires dans des pays tiers ou organisations internationales';
    case BUSINESS_OR_INSTITUTIONAL = 'Partenaires institutionnels ou commerciaux';
    case OTHER = 'Autre (Préciser)';
}
