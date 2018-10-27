<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 15/10/18
 * Time: 14:57.
 */

namespace APIBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RaidController extends AjaxAPIController
{
    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/organizer/raids")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listRaid(Request $request)
    {
        $user = $this->getUser();

        $raidManager = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Raid');

        $raids = $raidManager->findBy([
            'user' => $user,
        ]);

        $assetsManager = $this->get('assets.packages');
        $baseUrl = $request->getSchemeAndHttpHost();

        $dataRaids = [];
        foreach ($raids as $raid) {
            $raidArr = [];
            $raidArr['id'] = $raid->getId();
            $raidArr['name'] = $raid->getName();
            $raidArr['date'] = $raid->getDate();
            $raidArr['picture'] = $baseUrl . '/' . $assetsManager->getUrl('uploads/raids/' . $raid->getPicture());
            $raidArr['address'] = $raid->getAddress();
            $raidArr['addressAddition'] = $raid->getAddressAddition();
            $raidArr['postCode'] = $raid->getPostCode();
            $dataRaids[] = $raidArr;
        }

        return new JsonResponse($dataRaids);
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/helper/raids")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listRaidHelper(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $helperManager = $em->getRepository('AppBundle:Helper');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $helpers = $helperManager->findBy(array('user' => $user));

        $assetsManager = $this->get('assets.packages');
        $baseUrl = $request->getSchemeAndHttpHost();

        $raidIds = [];
        foreach ($helpers as $h) {
            $rid = $h->getRaid()->getId();
            $raidIds[] = $rid;
        }

        $raids = $raidManager->findBy([
            'id' => $raidIds,
        ]);

        $dataRaids = [];
        foreach ($raids as $raid) {
            $raidArr = [];
            $raidArr['id'] = $raid->getId();
            $raidArr['name'] = $raid->getName();
            $raidArr['date'] = $raid->getDate();
            $raidArr['picture'] = $baseUrl . '/' . $assetsManager->getUrl('uploads/raids/' . $raid->getPicture());
            $raidArr['address'] = $raid->getAddress();
            $raidArr['addressAddition'] = $raid->getAddressAddition();
            $raidArr['postCode'] = $raid->getPostCode();
            $dataRaids[] = $raidArr;
        }

        return new JsonResponse($dataRaids);
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/helper/raid/{raidId}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return JsonResponse
     */
    public function getRaidHelper(Request $request, $raidId)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $helperManager = $em->getRepository('AppBundle:Helper');
        $raidManager = $em->getRepository('AppBundle:Raid');

        $helper = $helperManager->findOneBy(array('user' => $user, 'raid' => $raidId));

        $assetsManager = $this->get('assets.packages');
        $baseUrl = $request->getSchemeAndHttpHost();

        if (null == $helper) {
            return parent::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Accès refusé.');
        }

        $raid = $raidManager->findOneBy(array('id' => $helper->getRaid()->getId()));

        $dataRaid = [];
        $dataRaid['id'] = $raid->getId();
        $dataRaid['name'] = $raid->getName();
        $dataRaid['date'] = $raid->getDate();
        $dataRaid['picture'] = $baseUrl . '/' . $assetsManager->getUrl('uploads/raids/' . $raid->getPicture());
        $dataRaid['address'] = $raid->getAddress();
        $dataRaid['addressAddition'] = $raid->getAddressAddition();
        $dataRaid['postCode'] = $raid->getPostCode();

        return new JsonResponse($dataRaid);
    }

/**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/organizer/raid/{id}")
     *
     * @param Request $request
     * @param int     $raidId  raid id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRaidAction(Request $request, $raidId)
    {
        return AjaxAPIController::buildJSONStatus(Response::HTTP_BAD_REQUEST, 'Not implemented');
    }
}
