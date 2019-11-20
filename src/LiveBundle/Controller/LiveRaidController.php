<?php

namespace LiveBundle\Controller;

use AppBundle\Controller\AjaxAPIController;
use AppBundle\Entity\Raid;
use AppBundle\Entity\TwitterApiData;
use AppBundle\Entity\RaceTiming;
use Doctrine\ORM\EntityManager;
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

        $host = ($request->server->get('HTTP_X_FORWARDED_HOST')) ?
            $request->getScheme() . '://' . $request->server->get('HTTP_X_FORWARDED_HOST') :
            $request->getScheme() . '://' . $request->server->get('HTTP_HOST');

        $meta['url'] = $host . $request->server->get('BASE') . $request->getPathInfo();
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

        $competitorsData = $this->getClassment($raid, $em);

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

        $competitorsData = $this->getClassment($raid, $em);

        return new Response(json_encode($competitorsData) ?? "[]");
    }

    /**
     * @param Raid          $raid Raid
     * @param EntityManager $em   Entity manager
     *
     * @return array
     */
    private function getClassment(Raid $raid, EntityManager $em)
    {
        $competitorManager = $em->getRepository('AppBundle:Competitor');
        $raceManager = $em->getRepository('AppBundle:Race');
        $raceTimingManager = $em->getRepository('AppBundle:RaceTiming');
        $raceTracksManager = $em->getRepository('AppBundle:RaceTrack');
        $raceCheckpointManager = $em->getRepository('AppBundle:RaceCheckpoint');

        $competitorsFinal = [];

        // Get all races.
        $races = $raceManager->findBy(['raid' => $raid]);

        foreach ($races as $race) {
            $position = 1;
            $competitorsDataTemp = [];
            $competitorsData = [];

            // Get all checkpoints ordered.
            $checkpoints = [];
            $raceTracks = $raceTracksManager->findBy(["race" => $race], ['order' => 'DESC']);

            foreach ($raceTracks as $track) {
                $raceCheckpoints = $raceCheckpointManager->findBy(
                    ["raceTrack" => $track],
                    ['order' => 'DESC']
                );

                $checkpoints = array_merge($checkpoints, $raceCheckpoints);
            }

            $lastCheckpoint = reset($checkpoints);
            $raceCompetitors = $competitorManager->findBy(['race' => $race]);

            foreach ($raceCompetitors as $competitor) {
                $rId = $race->getId();
                $rName = $race->getName();
                $rStartTime = $race->getStartTime();
                $rEndTime = $race->getEndTime();

                $lastCheckpointTiming = $raceTimingManager->findOneBy(
                    ['checkpoint' => $lastCheckpoint, 'competitor' => $competitor]
                );

                if (!is_null($rStartTime) && !is_null($lastCheckpointTiming)) {
                    $timing = date(
                        'H:i:s',
                        (($lastCheckpointTiming->getTime()->getTimestamp()) - ($rStartTime->getTimestamp()))
                    );

                    $fraud = $competitor->getIsFraud() ? true : false;
                } elseif (is_null($rEndTime) && !is_null($rStartTime)) {
                    $timing = date(
                        'H:i:s',
                        ((new \DateTime())->getTimestamp() - ($rStartTime->getTimestamp()))
                    );

                    $fraud = $competitor->getIsFraud() ? true : false;
                } elseif (!is_null($rEndTime) && !is_null($rStartTime)) {
                    $timing = date(
                        'H:i:s',
                        (($rEndTime)->getTimestamp() - ($rStartTime->getTimestamp()))
                    );

                    $fraud = $competitor->getIsFraud() ? true : false;
                }

                $competitorsDataTemp[$competitor->getId()] = [
                    'id' => $competitor->getId(),
                    'lastname' => $competitor->getCompetitor1(),
                    'firstname' => $competitor->getCompetitor2(),
                    'numbersign' => $competitor->getNumberSign(),
                    'category' => $competitor->getCategory(),
                    'race_name' => $rName,
                    'classment' => 0,
                    'timing' => $timing ?? 0,
                    'fraud' => $fraud ?? false,
                    'race_id' => $rId,
                ];
            }

            $competitorsIds = array_keys($competitorsDataTemp);

            foreach ($checkpoints as $checkpoint) {
                if (!empty($competitorsIds)) {
                    $partialClassment = $this->getPartialClassmentOnCheckpoint(
                        $checkpoint->getId(),
                        $competitorsIds,
                        $em
                    );

                    foreach ($partialClassment as $element) {
                        $key = array_search($element['competitor_id'], $competitorsIds);
                        unset($competitorsIds[$key]);
                        $competitorsDataTemp[$element['competitor_id']]['classment'] = $position++;
                        $competitorsData[] = $competitorsDataTemp[$element['competitor_id']];
                        unset($competitorsDataTemp[$element['competitor_id']]);
                    }
                }
            }

            $competitorsData = array_merge($competitorsData, $competitorsDataTemp);
            $competitorsFinal = array_merge($competitorsFinal, $competitorsData);
        }

        return $competitorsFinal;
    }

    /**
     * @param int           $checkpoint
     * @param array         $cids
     * @param EntityManager $em
     * @return mixed
     */
    private function getPartialClassmentOnCheckpoint($checkpoint, $cids, $em)
    {
        $rawSql = "SELECT rt.* " .
            "FROM race_timing rt " .
            "INNER JOIN (" .
            "    SELECT competitor_id, MAX(start_time) AS st " .
            "    FROM race_timing " .
            "    WHERE competitor_id IN (" . implode(',', $cids) . ") " .
            "    AND checkpoint_id = " . $checkpoint . " " .
            "    GROUP BY competitor_id" .
            ") ct " .
            "ON rt.competitor_id = ct.competitor_id " .
            "AND rt.start_time = ct.st";

        $stmt = $em->getConnection()->prepare($rawSql);
        $stmt->execute([]);

        return $stmt->fetchAll();
    }
}
