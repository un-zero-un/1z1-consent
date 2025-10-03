<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Behavior\HasAgency;
use App\Entity\AdminUser;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist)]
final readonly class SetAgencyEventListener
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof HasAgency) {
            return;
        }

        if ($entity->getAgency()) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof AdminUser) {
            return;
        }

        $entity->setAgency($user->getAgency());
    }
}
