<?php

namespace AppBundle\Service;

use AppBundle\Entity\SportType;
use Doctrine\ORM\EntityManagerInterface;

class SportTypeService
{
    /**
     * SportTypeService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $sportTypes
     *
     * @return false|string
     */
    public function sportTypesArrayToJson($sportTypes)
    {
        $sportTypesObj = [];

        foreach ($sportTypes as $sportType) {
            $obj = [];

            $obj['id'] = $sportType->getId();
            $obj['sport'] = $sportType->getSport();
            $obj['icon'] = $sportType->getIcon();

            $sportTypesObj[] = $obj;
        }

        return json_encode($sportTypesObj);
    }
}
