<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 08/02/19
 * Time: 15:17
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\Message;
use AppBundle\Entity\PoiType;
use AppBundle\Entity\Raid;
use AppBundle\Service\MessageService;
use PHPUnit\Framework\TestCase;

class MessageServiceTest extends TestCase
{
    private $container;

    /** @var MessageService $messageService */
    private $messageService;

    public function __construct(){
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->messageService = $this->container->get('MessageService');
    }

    public function testMessagesToJson()
    {
        $dateTime = new \DateTime();
        $time = json_encode($dateTime);

        $poiType = new PoiType();
        $poiType->setType("test");

        $message = new Message();
        $message->setRaid(new Raid());
        $message->setType("type");
        $message->setDatetime($dateTime);
        $message->setTargetPoitypes($poiType);

        $message2 = new Message();
        $message2->setRaid(new Raid());
        $message2->setType("type");
        $message2->setDatetime($dateTime);
        $message2->setTargetPoitypes($poiType);

        $json = $this->messageService->messagesToJson([$message, $message2]);

        self::assertEquals("{\"messages\":[{\"id\":null,\"text\":\"\",\"type\":\"type\",\"datetime\":$time},{\"id\":null,\"text\":\"\",\"type\":\"type\",\"datetime\":$time}]}", $json);


    }

}