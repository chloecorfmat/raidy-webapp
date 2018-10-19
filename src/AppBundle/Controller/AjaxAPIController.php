<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 17/10/18
 * Time: 15:10
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
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

        $res = new JsonResponse($ret);
        $res->setStatusCode($code);

        return $res;
    }

    /**
     * @param int   $code
     * @param mixed $data
     * @return Response
     */
    public function buildJSONReturn($code, $data)
    {
        $res = new JsonResponse($data);
        $res->setStatusCode($code);

        return $res;
    }
}
