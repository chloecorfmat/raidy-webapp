<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 10:03
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\SportType;
use AppBundle\Service\SportTypeService;
use PHPUnit\Framework\TestCase;

class SportTypeServiceTest extends TestCase
{
    private $container;

    /** @var SportTypeService $sportTypeService */
    private $sportTypeService;

    public function __construct()
    {
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->sportTypeService = $this->container->get('SportTypeService');
    }

    public function testSportTypesArrayToJson()
    {
        $sportType = new SportType();
        $sportType->setId(1);
        $sportType->setIcon("132.jpg");
        $sportType->setSport("VTT");

        $obj = [];
        $obj[] = $sportType;

        $json = $this->sportTypeService->sportTypesArrayToJson($obj);

        $this->assertEquals('[{"id":1,"sport":"VTT","icon":"132.jpg"}]', $json);
    }
}