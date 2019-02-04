<?php

namespace LiveBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\TwitterApiData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class LiveRaidController extends AjaxAPIController
{
    /**
     * @Route("/live/raid/{id}", name="live")
     *
     * @param Request $request request
     * @param int     $id      raid identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function raidLive(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $raidManager = $em->getRepository('AppBundle:Raid');

        //$raid = $raidManager->find($id);
        $raid = $raidManager->findOneBy(['uniqid' => $id]);

        if (null == $raid) {
            throw $this->createNotFoundException('Ce raid n\'existe pas');
        }

        $meta['url'] = $request->getSchemeAndHttpHost() . $request->getPathInfo();
        $meta['title'] = 'Live | Raidy';
        $meta['image'] = '/uploads/raids/' . $raid->getPicture();
        $meta['description'] = 'Accéder au live de raids';

        // Get tweets.
        $twitterService = $this->container->get('TwitterService');

        // API used : https://developer.twitter.com/en/docs/tweets/search/overview.
        $url = 'https://api.twitter.com/1.1/search/tweets.json';

        // Get fields according to data saved in database.
        $twitterAccounts = explode(',', str_replace(' ', '', $raid->getTwitterAccounts()));

        foreach ($twitterAccounts as $key => $account) {
            $twitterAccounts[$key] = '@' . $account;
        }

        $twitterHashtags = explode(',', str_replace(' ', '', $raid->getTwitterHashtags()));

        $filters = array_merge($twitterAccounts, $twitterHashtags);

        $data = '';

        foreach ($filters as $key => $filter) {
            if ('@' !== $filter && '' !== $filter) {
                $data .= $filter;

                if (next($filters)) {
                    $data .= '%20OR%20';
                }
            }
        }

        $getfield = '?q=' . $data . '&result_type=recent&include_entities=false';
        $requestMethod = 'GET';

        $jsonResults = $twitterService->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        // This is passed to vuejs component.
        $tweets = json_decode($jsonResults)->statuses ?? '';

        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $competitors = $competitorManager->findBy(['raid' => $raid]);

        $competitorsData = [];

        foreach ($competitors as $competitor) {
            if ($competitor->getRace()) {
                $r = $competitor->getRace()->getId();
            } else {
                $r = null;
            }

            $competitorsData[] = [
                'id' => $competitor->getId(),
                'lastname' => $competitor->getLastname(),
                'firstname' => $competitor->getFirstname(),
                'numbersign' => $competitor->getNumberSign(),
                'race_id' => $r,
            ];
        }

        $raceManager = $em->getRepository('AppBundle:Race');
        $races = $raceManager->findBy(['raid' => $raid]);

        $racesData = [];
        foreach ($races as $race) {
            $racesData[] = [
                'id' => $race->getId(),
                'name' => $race->getName(),
            ];
        }

        return $this->render('LiveBundle:Raid:raid.html.twig', [
            'raid' => $raid,
            'meta' => $meta,
            'tweets' => json_encode($tweets) ?? "[]",
            'twitter_activation' => $tweets ? true:false,
            'competitors' => json_encode($competitorsData) ?? "[]",
            'via' => $this->container->getParameter('app.twitter.account'),
            'races' => json_encode($racesData) ?? "[]",
        ]);
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/public/raid/{raidId}/tweets")
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return Response
     */
    public function tweets(Request $request, $raidId)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        $twitterApiDataManager = $em->getRepository('AppBundle:TwitterApiData');
        $data = $twitterApiDataManager->findBy(['raid' => $raid], ['id' => 'desc'], 1);
        $now = new \DateTime();

        if (!empty($data)) {
            $interval = $now->getTimestamp() - $data[0]->getRequestDatetime()->getTimestamp();
        }

        if (empty($data) || ($interval > 60)) {
            // Get tweets.
            $twitterService = $this->container->get('TwitterService');

            // API used : https://developer.twitter.com/en/docs/tweets/search/overview.
            $url = 'https://api.twitter.com/1.1/search/tweets.json';

            // Get fields according to data saved in database.
            $twitterAccounts = explode(',', str_replace(' ', '', $raid->getTwitterAccounts()));

            foreach ($twitterAccounts as $key => $account) {
                $twitterAccounts[$key] = '@' . $account;
            }

            $twitterHashtags = explode(',', str_replace(' ', '', $raid->getTwitterHashtags()));

            $filters = array_merge($twitterAccounts, $twitterHashtags);

            $data = '';

            foreach ($filters as $key => $filter) {
                if ('@' !== $filter && '' !== $filter) {
                    $data .= $filter;

                    if (next($filters)) {
                        $data .= '%20OR%20';
                    }
                }
            }

            $getfield = '?q=' . $data . '&result_type=recent&include_entities=false';
            $requestMethod = 'GET';

            $jsonResults = $twitterService->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest();

            // This is passed to vuejs component.
            $tweets = json_decode($jsonResults)->statuses ?? '';

            $twitterApiData = new TwitterApiData();
            $twitterApiData->setRaid($raid);
            $twitterApiData->setData(json_encode($tweets) ?? "[]");
            $twitterApiData->setRequestDatetime($now);
            $em->persist($twitterApiData);
            $em->flush();

            return new Response($twitterApiData->getData());
        }

        return new Response($data[0]->getData());
    }

    /**
     * @Rest\View(serializerGroups={"secured"})
     * @Rest\Get("/api/public/raid/{raidId}/competitors")
     *
     * @param Request $request request
     * @param int     $raidId  raid identifier
     *
     * @return Response
     */
    public function competitors(Request $request, $raidId)
    {
        $em = $this->getDoctrine()->getManager();

        $raidManager = $em->getRepository('AppBundle:Raid');
        $raid = $raidManager->findOneBy(array('uniqid' => $raidId));

        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $competitors = $competitorManager->findBy(['raid' => $raid]);

        $competitorsData = [];

        foreach ($competitors as $competitor) {
            if ($competitor->getRace()) {
                $r = $competitor->getRace()->getId();
            } else {
                $r = null;
            }

            $competitorsData[] = [
                'id' => $competitor->getId(),
                'lastname' => $competitor->getLastname(),
                'firstname' => $competitor->getFirstname(),
                'numbersign' => $competitor->getNumberSign(),
                'race_id' => $r,
            ];
        }

        return new Response(json_encode($competitorsData) ?? "[]");
    }
}
