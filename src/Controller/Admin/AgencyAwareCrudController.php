<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\Agency;
use App\Exception\InvalidUserException;
use App\Exception\UserHasNoAgencyException;

trait AgencyAwareCrudController
{
    /**
     * @throws \RuntimeException
     */
    public function getAgency(): Agency
    {
        $user = $this->getUser();
        if (!$user instanceof AdminUser) {
            throw new InvalidUserException($user);
        }

        $agency = $user->getAgency();
        if (!$agency) {
            throw new UserHasNoAgencyException();
        }

        return $agency;
    }
}
