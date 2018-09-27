<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 25/09/18
 * Time: 12:46
 */

namespace AppBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    protected $plainPassword;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}