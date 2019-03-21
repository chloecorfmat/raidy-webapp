<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 08/02/19
 * Time: 14:59
 */

namespace Tests\AppBundle\Service;


use AppBundle\Entity\Competitor;
use AppBundle\Entity\Race;
use AppBundle\Entity\Raid;
use AppBundle\Service\CompetitorService;
use PHPUnit\Framework\TestCase;

class CompetitorServiceTest extends TestCase
{

    private $container;

    /** @var CompetitorService $competitorService */
    private $competitorService;

    public function __construct(){
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->competitorService = $this->container->get('CompetitorService');
    }

    public function testCompetitorToJson()
    {
        $race = new Race();
        $raid = new Raid();

        $birthdate = new \DateTime();

        $competitor = new Competitor();
        $competitor->setId(1);
        $competitor->setRace($race);
        $competitor->setRaid($raid);
        $competitor->setIsFraud(false);
        $competitor->setNFCSerialId("XXXXX");
        $competitor->setBirthYear(1995);
        $competitor->setCategory("PRO");
        $competitor->setCompetitor1("competitor1");
        $competitor->setCompetitor2("competitor2");
        $competitor->setNumberSign("12");
        $competitor->setSex("M");
        $competitor->setUniqid("AZERTY");

        $json = $this->competitorService->competitorToJson($competitor);

        self::assertEquals("{\"id\":1,\"competitor1\":\"competitor1\",\"competitor2\":\"competitor2\",\"number_sign\":\"12\",\"nfc_serial_id\":\"XXXXX\",\"category\":\"PRO\",\"sex\":\"M\",\"birthyear\":1995,\"race\":null,\"raid\":null}", $json);
    }

    public function testCompetitorsArrayToJson()
    {
        $race = new Race();
        $raid = new Raid();

        $birthdate = new \DateTime();

        $competitor = new Competitor();
        $competitor->setId(1);
        $competitor->setRace($race);
        $competitor->setRaid($raid);
        $competitor->setIsFraud(false);
        $competitor->setNFCSerialId("XXXXX");
        $competitor->setBirthYear(1995);
        $competitor->setCategory("PRO");
        $competitor->setCompetitor1("competitor11");
        $competitor->setCompetitor2("competitor12");
        $competitor->setNumberSign("12");
        $competitor->setSex("M");
        $competitor->setUniqid("AZERTY");

        $competitor2 = new Competitor();
        $competitor2->setId(1);
        $competitor2->setRace($race);
        $competitor2->setRaid($raid);
        $competitor2->setIsFraud(false);
        $competitor2->setNFCSerialId("XXXXX");
        $competitor2->setBirthYear(1995);
        $competitor2->setCategory("PRO");
        $competitor2->setCompetitor1("competitor21");
        $competitor2->setCompetitor2("competitor22");
        $competitor2->setNumberSign("12");
        $competitor2->setSex("M");
        $competitor2->setUniqid("AZERTY");


        $json = $this->competitorService->competitorsArrayToJson([$competitor, $competitor2]);

        self::assertEquals("[{\"id\":\"AZERTY\",\"competitor1\":\"competitor11\",\"competitor2\":\"competitor12\",\"number_sign\":\"12\",\"nfc_serial_id\":\"XXXXX\",\"category\":\"PRO\",\"sex\":\"M\",\"birthyear\":1995,\"race\":{\"id\":null,\"name\":null},\"raid\":null},{\"id\":\"AZERTY\",\"competitor1\":\"competitor21\",\"competitor2\":\"competitor22\",\"number_sign\":\"12\",\"nfc_serial_id\":\"XXXXX\",\"category\":\"PRO\",\"sex\":\"M\",\"birthyear\":1995,\"race\":{\"id\":null,\"name\":null},\"raid\":null}]", $json);
    }

    public function testCheckRaceTimingData()
    {
        $data = [];
        $data['NFCSerialId'] = "XXX";
        $data['time'] = new \DateTime();
        $data['poi_id'] = 1;
        $check = $this->competitorService->checkRaceTimingData($data);
        self::assertTrue($check);

        $data = [];
        $data['NFCSerialId'] = "XXX";
        $data['time'] = new \DateTime();
        $check = $this->competitorService->checkRaceTimingData($data);
        self::assertFalse($check);

        $data = [];
        $data['NFCSerialId'] = "XXX";
        $data['time'] = new \DateTime();
        $data['poi_id'] = null;
        $check = $this->competitorService->checkRaceTimingData($data);
        self::assertFalse($check);
    }

}
