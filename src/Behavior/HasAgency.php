<?php

declare(strict_types=1);

namespace App\Behavior;

use App\Entity\Agency;

interface HasAgency
{
    public function getAgency(): ?Agency;

    public function setAgency(?Agency $agency): void;
}
