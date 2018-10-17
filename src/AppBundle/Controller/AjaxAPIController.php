<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 17/10/18
 * Time: 15:10
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AjaxAPIController extends Controller
{
     /**
     * @param int   $code
     * @param mixed $message
     * @return Response
     */
    public function buildJSONStatus($code, $message)
    {
        $ret = [];
        $ret['code'] = $code;
        $ret['message'] = $message;

        $res = new Response(json_encode($ret));
        $res->setStatusCode($code);
        $res->headers->set('Content-Type', 'application/json');

        return $res;
    }

    /**
     * @param int   $code
     * @param mixed $message
     * @param mixed $data
     * @return Response
     */
    public function buildJSONReturn($code, $message, $data)
    {
        $res = new Response(json_encode($data));
        $res->setStatusCode($code);
        $res->headers->set('Content-Type', 'application/json');

        return $res;
    }
}
