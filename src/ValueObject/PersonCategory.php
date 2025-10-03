<?php

declare(strict_types=1);

namespace App\ValueObject;

enum PersonCategory: string
{
    case EMPLOYEE = 'Salariés';
    case INTERN = 'Services Internes';
    case CLIENT = 'Clients';
    case PROVIDER = 'Fournisseurs';
    case CONTRACTOR = 'Prestataires';
    case LEAD = 'Prospects';
    case APPLICANT = 'Candidats';
    case OTHER = 'Autres (préciser)';
}
