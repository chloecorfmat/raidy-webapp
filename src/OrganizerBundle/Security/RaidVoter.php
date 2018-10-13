<?php

namespace OrganizerBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Raid;

class RaidVoter implements VoterInterface
{

    const EDIT = 'edit';

    /**
     * @param TokenInterface $token
     * @param mixed          $raid
     * @param array          $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $raid, array $attributes)
    {
        if ($this->supports($attributes, $raid)) {
            $user = $token->getUser();
            // If current user is the creator or a Super Admin
            if ($user->getId() ==  $raid->getUser()->getId() || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                return VoterInterface::ACCESS_GRANTED;
            } else {
                return Voter::ACCESS_DENIED;
            }
        } else {
            return Voter::ACCESS_ABSTAIN;
        }
    }

    /**
     * @param mixed $attribute attribute list
     * @param mixed $object
     * @return bool support
     */
    protected function supports($attribute, $object)
    {
        if (in_array($attribute, array(self::EDIT))) {
            return false;
        }

        if (!$object instanceof Raid) {
            return false;
        }

        return true;
    }
}
