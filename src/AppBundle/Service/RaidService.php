<?php
/**
 * Created by PhpStorm.
 * User: anais
 * Date: 25/10/2018
 * Time: 22:42.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Raid;
use Doctrine\ORM\EntityManagerInterface;

class RaidService
{
    /**
     * RaidService constructor.
     *
     * @param EntityManagerInterface $em
     * @param UploadedFileService    $uploadedFileService
     */
    public function __construct(EntityManagerInterface $em, UploadedFileService $uploadedFileService)
    {
        $this->em = $em;
        $this->uploadedFileService = $uploadedFileService;
    }

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
            $obj['uniqid'] = $raid->getUniqid();

            $raidsObj[] = $obj;
        }

        return json_encode($raidsObj);
    }

    /**
     * @param Raid $raid
     *
     * @return false|string
     */
    public function raidToJson($raid)
    {
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
        $obj['uniqid'] = $raid->getUniqid();

        return json_encode($obj);
    }

    /**
     * @param Raid   $obj
     * @param string $directory
     * @param string $oldPicture
     *
     * @return Raid
     */
    public function cloneRaid($obj, $directory, $oldPicture)
    {
        $raid = new Raid();

        $raid->setName($obj->getName());
        $raid->setDate($obj->getDate());
        $raid->setAddress($obj->getAddress());

        if (null != $obj->getAddressAddition()) {
            $raid->setAddressAddition($obj->getAddressAddition());
        } else {
            $raid->setAddressAddition(null);
        }

        $raid->setPostCode($obj->getPostCode());
        $raid->setCity($obj->getCity());
        $raid->setEditionNumber($obj->getEditionNumber());

        if (null != $obj->getPicture()) {
            $picture = $this->uploadedFileService->saveFile($obj->getPicture(), $directory);
            $raid->setPicture($picture);
        } else {
            $raid->setPicture($oldPicture);
        }

        $raid->setUniqid(uniqid());

        $userRepository = $this->em->getRepository('AppBundle:User');
        $user = $userRepository->find($obj->getUser());
        $raid->setUser($user);

        $this->em->persist($raid);
        $this->em->flush();

        return $raid;
    }
}
