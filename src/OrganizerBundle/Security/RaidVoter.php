<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace OrganizerBundle\Security;

use AppBundle\Entity\Raid;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RaidVoter implements VoterInterface
{
    const EDIT = 'edit';

    /**
     * @param TokenInterface $token
     * @param mixed          $raid
     * @param array          $attributes
     *
     * @return int
     */
    public function vote(TokenInterface $token, $raid, array $attributes)
    {
        if ($this->supports($attributes, $raid)) {
            $user = $token->getUser();
            // If current user is the creator or a Super Admin
            if ($user->getId() === $raid->getUser()->getId()
                || \in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
                return VoterInterface::ACCESS_GRANTED;
            }

            return Voter::ACCESS_DENIED;
        }

        return Voter::ACCESS_ABSTAIN;
    }

    /**
     * @param mixed $attribute attribute list
     * @param mixed $object
     *
     * @return bool support
     */
    protected function supports($attribute, $object)
    {
        if (\in_array($attribute, [self::EDIT], true)) {
            return false;
        }

        if (!$object instanceof Raid) {
            return false;
        }

        return true;
    }
}
