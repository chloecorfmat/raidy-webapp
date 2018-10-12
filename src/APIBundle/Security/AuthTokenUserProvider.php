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

namespace APIBundle\Security;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthTokenUserProvider implements UserProviderInterface
{
    protected $authTokenRepository;
    protected $userRepository;

    /**
     * AuthTokenUserProvider constructor.
     *
     * @param EntityRepository $authTokenRepository auth token repository
     * @param EntityRepository $userRepository      user repository
     */
    public function __construct(EntityRepository $authTokenRepository, EntityRepository $userRepository)
    {
        $this->authTokenRepository = $authTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $authTokenHeader auth token header
     *
     * @return mixed
     */
    public function getAuthToken($authTokenHeader)
    {
        return $this->authTokenRepository->findOneByValue($authTokenHeader);
    }

    /**
     * @param string $email user email
     *
     * @return UserInterface
     */
    public function loadUserByUsername($email)
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * @param UserInterface $user user
     *
     * @return UserInterface|void
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    /**
     * @param string $class class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'AppBundle\Entity\User' === $class;
    }
}
