<?php

namespace LiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class LiveRaidController extends Controller
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
        // @TODO.
        $meta['image'] = '/uploads/raids/dc015d1aa7f746d65707ce2815452229.png';
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

        $tweets = json_decode($jsonResults);

        return $this->render('LiveBundle:Raid:raid.html.twig', [
            'raid' => $raid,
            'meta' => $meta,
            'tweets' => $tweets->statuses ?? '',
            'via' => $this->container->getParameter('app.twitter.account'),
        ]);
    }
}
