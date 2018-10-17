<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/10/18
 * Time: 14:57
 */

namespace APIBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RaidController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/organizer/raids")
     *
     * @return JsonResponse
     */
    public function listRaid()
    {
        $user = $this->getUser();

        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        $raids = $raidManager->findBy([
            'user' => $user,
        ]);

        $dataRaids = [];
        foreach ($raids as $raid) {
            $raidArr = [];
            $raidArr['id'] = $raid->getId();
            $raidArr['name'] = $raid->getName();
            $raidArr['date'] = $raid->getDate();
            $raidArr['address'] = $raid->getAddress();
            $raidArr['addressAddition'] = $raid->getAddressAddition();
            $raidArr['postCode'] = $raid->getPostCode();
            $dataRaids[] = $raidArr;
        }

        return new JsonResponse($dataRaids);
    }
}