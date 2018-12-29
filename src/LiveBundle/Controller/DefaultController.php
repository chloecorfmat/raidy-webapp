<?php

namespace LiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/live")
     *
     * @param Request $request request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $meta['url'] = $request->getSchemeAndHttpHost() . $request->getPathInfo();
        $meta['title'] = 'Live | Raidy';
        $meta['image'] = '/uploads/raids/dc015d1aa7f746d65707ce2815452229.png';
        $meta['description'] = 'Accéder au live de raids';

        $via = $this->container->getParameter('app.twitter.account');

        return $this->render('LiveBundle:Default:index.html.twig', compact('meta', 'via'));
    }
}
