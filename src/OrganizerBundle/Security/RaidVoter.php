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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RaidVoter implements VoterInterface
{
    const EDIT = 'edit';
    const COLLAB = 'collab';
    const HELPER = 'helper';

    private $em;

    /**
     * RaidVoter constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

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
            $isOwner = $user->getId() === $raid->getUser()->getId();

            $isCollaborator = false;
            $isHelper = false;

            if (in_array(self::EDIT, $attributes)) {
                if (!$isOwner) {
                    $collaborationManager = $this->em->getRepository('AppBundle:Collaboration');
                    $collaboration = $collaborationManager->findOneBy(["user" => $user, "raid" => $raid]);

                    if ($collaboration != null) {
                        $isCollaborator = true;
                    }
                }
            }

            if (in_array(self::HELPER, $attributes)) {
                if (!$isHelper) {
                    $helperManager = $this->em->getRepository('AppBundle:Helper');
                    $helper = $helperManager->findOneBy(["user" => $user, "raid" => $raid]);

                    if ($helper != null) {
                        $isHelper = true;
                    }
                }
            }

            if ($isOwner || $isCollaborator || $isHelper) {
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
        if (\in_array($attribute, [self::EDIT, self::COLLAB], true)) {
            return false;
        }

        if (!$object instanceof Raid) {
            return false;
        }

        return true;
    }
}
