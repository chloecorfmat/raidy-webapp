<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 18/10/2018
 * Time: 16:32.
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class PoiTypeService
{
    /**
     * PoiTypeService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $poiTypes
     *
     * @return false|string
     */
    public function poisArrayToJson($poiTypes)
    {
        $poiTypesObj = [];

        foreach ($poiTypes as $poiType) {
            $obj = [];

            $obj['id'] = $poiType->getId();
            $obj['type'] = $poiType->getType();
            $obj['color'] = $poiType->getColor();

            $poiTypesObj[] = $obj;
        }

        return json_encode($poiTypesObj);
    }
}
