<?php

declare(strict_types=1);

namespace App\Security;

use App\Behavior\HasAgency;
use App\Behavior\IndirectlyHasAgency;
use App\Entity\AdminUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @psalm-suppress UnusedClass
 *
 * @extends Voter<string, HasAgency|IndirectlyHasAgency|null>
 */
final class IsOwnerVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if ('IS_OWNER' !== $attribute) {
            return false;
        }

        return null === $subject || $subject instanceof HasAgency || $subject instanceof IndirectlyHasAgency;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (null === $subject) {
            return true;
        }

        if (!($subject instanceof HasAgency || $subject instanceof IndirectlyHasAgency)) {
            throw new \InvalidArgumentException('Subject must be an instance of HasAgency or IndirectlyHasAgency');
        }

        $user = $token->getUser();
        if (!$user instanceof AdminUser) {
            throw new \InvalidArgumentException('This user isn\'t an admin user');
        }

        return $subject->getAgency() === $user->getAgency();
    }
}
