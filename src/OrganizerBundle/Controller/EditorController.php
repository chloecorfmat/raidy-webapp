<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 10/10/2018
 * Time: 09:53.
 */

namespace OrganizerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class EditorController extends Controller
{
    /**
     * @Route("/organizer/editor/{id}")
     *
     * @param Request $request request
     * @param mixed   $id      id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editorAction(Request $request, $id)
    {
        return $this->render('OrganizerBundle:Editor:editor.html.twig');
    }
}
