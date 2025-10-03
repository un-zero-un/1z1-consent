<?php

declare(strict_types=1);

namespace App\Behavior;

use App\Entity\Agency;

interface IndirectlyHasAgency
{
    public function getAgency(): ?Agency;
}
