<?php

namespace AppBundle\Service;

use AppBundle\Entity\Helper;
use Doctrine\ORM\EntityManagerInterface;

class HelperService
{
    /**
     * HelperService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Helper $helper
     * @param int    $raidId
     * @param mixed  $obj
     *
     * @return mixed
     */
    public function updateHelperToPoiFromArray($helper, $raidId, $obj)
    {
        $poiRepository = $this->em->getRepository('AppBundle:Poi');
        $poi = $poiRepository->find($obj['poi']);
        $helper->setPoi($poi);

        return $helper;
    }

    /**
     * @param Helper $helper
     *
     * @return false|string
     */
    public function helperToJson($helper)
    {
        $obj = [];

        $obj['id'] = $helper->getId();
        $obj['user'] = $helper->getUser()->getId();
        $obj['isCheckedIn'] = $helper->getisCheckedIn();

        if (null != $helper->getPoi()) {
            $obj['poi'] = $helper->getPoi()->getId();
        } else {
            $obj['poi'] = '';
        }

        $obj['favoritePoiType'] = $helper->getFavoritePoiType()->getId();

        if (null != $helper->getCheckInTime()) {
            $obj['checkInTime'] = $helper->getCheckInTime()->getId();
        } else {
            $obj['checkInTime'] = '';
        }

        $obj['raid'] = $helper->getRaid()->getId();

        return json_encode($obj);
    }

    /**
     * @param mixed $obj
     * @param bool  $checkId
     *
     * @return bool
     */
    public function checkDataAffectationArray($obj, $checkId)
    {
        $status = true;

        if ($checkId) {
            if (null == $obj['id'] || '' == $obj['id']) {
                $status = false;
            }
        }

        if (null == $obj['poi'] || '' == $obj['poi']) {
            $status = false;
        }

        return $status;
    }
}
