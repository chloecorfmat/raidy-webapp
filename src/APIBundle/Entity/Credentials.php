<?php

namespace APIBundle\Entity;

class Credentials
{
    protected $login;

    protected $password;

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}
