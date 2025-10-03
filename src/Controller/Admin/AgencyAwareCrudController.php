<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\Agency;

trait AgencyAwareCrudController
{
    /**
     * @throws \RuntimeException
     */
    public function getAgency(): Agency
    {
        $user = $this->getUser();
        if (!$user instanceof AdminUser) {
            throw new \RuntimeException('No user, or user isn\'t an admin user');
        }

        $agency = $user->getAgency();
        if (!$agency) {
            throw new \RuntimeException('No agency found');
        }

        return $agency;
    }
}
