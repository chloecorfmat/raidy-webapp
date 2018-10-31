<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ProfileService
{
    /**
     * @param User $user
     *
     * @return false|string
     */
    public function profileToJson($user)
    {
        $obj = [];

        $obj['username'] = $user->getUsername();
        $obj['firstname'] = $user->getFirstName();
        $obj['lastname'] = $user->getLastName();
        $obj['email'] = $user->getEmail();
        $obj['phone'] = $user->getPhone();

        return json_encode($obj);
    }

    /**
     * @param User  $user
     * @param mixed $obj
     *
     * @return mixed
     */
    public function updateProfileFromArray($user, $obj)
    {
        $user->setUsername($obj['username']);
        $user->setFirstName($obj['firstname']);
        $user->setLastName($obj['lastname']);
        $user->setEmail($obj['email']);
        $user->setPhone($obj['phone']);

        return $user;
    }
}
