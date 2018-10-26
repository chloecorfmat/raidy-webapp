<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 25/10/2018
 * Time: 22:42
 */

namespace AppBundle\Service;

use AppBundle\Entity\Raid;
use Doctrine\ORM\EntityManagerInterface;

class RaidService
{

    /**
     * @param array $raids
     *
     * @return false|string
     */
    public function raidsArrayToJson($raids)
    {
        $raidsObj = [];

        foreach ($raids as $raid) {
            $obj = [];

            $obj['id'] = $raid->getId();
            $obj['name'] = $raid->getName();
            $obj['date'] = $raid->getDate();
            $obj['address'] = $raid->getAddress();

            if (null != $raid->getAddressAddition()) {
                $obj['addressAddition'] = $raid->getAddressAddition();
            } else {
                $obj['addressAddition'] = '';
            }

            $obj['postCode'] = $raid->getPostCode();
            $obj['city'] = $raid->getCity();
            $obj['editionNumber'] = $raid->getEditionNumber();
            $obj['picture'] = $raid->getPicture();

            $raidsObj[] = $obj;
        }

        return json_encode($raidsObj);
    }
}
