<?php

namespace AppBundle\Service;

use AppBundle\Entity\PoiType;
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
     * @param mixed $obj
     * @param mixed $raidId
     *
     * @return PoiType
     */
    public function poiTypeFromForm($obj, $raidId)
    {
        $poiType = new PoiType();

        $poiType->setType($obj->getType());
        $poiType->setColor($obj->getColor());

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $poiType->setRaid($raid);

        return $poiType;
    }

    /**
     * @param PoiType $poiType
     * @param int     $raidId
     * @param mixed   $obj
     *
     * @return mixed
     */
    public function updatePoiTypeFromForm($poiType, $raidId, $obj)
    {
        $poiType->setType($obj->getType());
        $poiType->setColor($obj->getColor());

        $raidRepository = $this->em->getRepository('AppBundle:Raid');
        $raid = $raidRepository->find($raidId);
        $poiType->setRaid($raid);

        return $poiType;
    }

    /**
     * @param array $poiTypes
     *
     * @return false|string
     */
    public function poiTypesArrayToJson($poiTypes)
    {
        $poiTypesObj = [];

        foreach ($poiTypes as $poiType) {
            $obj = [];

            $obj['id'] = $poiType->getId();
            $obj['type'] = $poiType->getType();
            $obj['color'] = $poiType->getColor();
            $obj['raid'] = $poiType->getRaid()->getId();

            $poiTypesObj[] = $obj;
        }

        return json_encode($poiTypesObj);
    }
}
