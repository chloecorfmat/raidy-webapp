<?php

namespace Tests\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use APIBundle\Controller\AuthTokenController;
use Symfony\Component\HttpFoundation\Response;


class AuthTokenControllerTest extends WebTestCase
{
    function testinvalidCredentials(){
        $controller = new AuthTokenController();
        $res = $controller->invalidCredentials();

        $content = json_decode($res->getContent());
        $internalCode = $content->code;
        $internalMessage = $content->message;

        $this->assertEquals($res->getStatusCode(), Response::HTTP_BAD_REQUEST);
        $this->assertEquals($internalCode, Response::HTTP_BAD_REQUEST);
        $this->assertEquals($internalMessage, 'Bad credentials');
    }
}