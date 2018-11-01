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
     * @param mixed $userId
     *
     * @return PoiType
     */
    public function poiTypeFromForm($obj, $userId)
    {
        $poiType = new PoiType();

        $poiType->setType($obj->getType());
        $poiType->setColor($obj->getColor());

        $userRepository = $this->em->getRepository('AppBundle:User');
        $user = $userRepository->find($userId);
        $poiType->setUser($user);

        return $poiType;
    }

    /**
     * @param PoiType $poiType
     * @param int     $userId
     * @param mixed   $obj
     *
     * @return mixed
     */
    public function updatePoiTypeFromForm($poiType, $userId, $obj)
    {
        $poiType->setType($obj->getType());
        $poiType->setColor($obj->getColor());

        $userRepository = $this->em->getRepository('AppBundle:User');
        $user = $userRepository->find($userId);
        $poiType->setUser($user);

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
            $obj['user'] = $poiType->getUser()->getId();

            $poiTypesObj[] = $obj;
        }

        return json_encode($poiTypesObj);
    }
}
