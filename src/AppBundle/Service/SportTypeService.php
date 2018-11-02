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
     * @param UploadedFileService    $uploadedFileService
     */
    public function __construct(EntityManagerInterface $em, UploadedFileService $uploadedFileService)
    {
        $this->em = $em;
        $this->uploadedFileService = $uploadedFileService;
    }

    /**
     * @param mixed $obj
     * @param mixed $directory
     *
     * @return SportType
     */
    public function sportTypeFromForm($obj, $directory)
    {
        $sportType = new SportType();

        $sportType->setSport($obj->getSport());

        $icon = $this->uploadedFileService->saveFile($obj->getIcon(), $directory);
        $sportType->setIcon($icon);

        return $sportType;
    }

    /**
     * @param SportType $sportType
     * @param mixed     $obj
     * @param mixed     $oldIcon
     * @param mixed     $directory
     *
     * @return mixed
     */
    public function updatePoiTypeFromForm($sportType, $obj, $oldIcon, $directory)
    {
        $sportType->setSport($obj->getSport());

        if (null !== $obj->getIcon()) {
            $icon = $this->uploadedFileService->saveFile($obj->getIcon(), $directory);
            $sportType->setIcon($icon);
        } else {
            $sportType->setIcon($oldIcon);
        }

        return $sportType;
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
