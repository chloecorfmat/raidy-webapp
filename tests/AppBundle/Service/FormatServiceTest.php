<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 23/11/18
 * Time: 08:39
 */

namespace Tests\AppBundle\Service;


use AppBundle\Service\FormatService;
use PHPUnit\Framework\TestCase;

class FormatServiceTest extends TestCase
{

    private $container;

    /** @var FormatService $formatService */
    private $formatService;

    public function __construct()
    {
        parent::__construct(null,[],'');
        $kernel = new \AppKernel('dev', false);
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->formatService = $this->container->get('FormatService');
    }

    public function testTelephoneNumber()
    {
        $phone1 = "06.13.86.58.38";
        $formattedPhoneNumber1 = $this->formatService->telephoneNumber($phone1);
        $this->assertEquals("0613865838", $formattedPhoneNumber1);

        $phone2 = "06-13-86-58-38";
        $formattedPhoneNumber2 = $this->formatService->telephoneNumber($phone2);
        $this->assertEquals("0613865838", $formattedPhoneNumber2);

        $phone3 = "06 13 86 58 38";
        $formattedPhoneNumber3 = $this->formatService->telephoneNumber($phone3);
        $this->assertEquals("0613865838", $formattedPhoneNumber3);

        $phone4 = "06.13-86 5838";
        $formattedPhoneNumber4 = $this->formatService->telephoneNumber($phone4);
        $this->assertEquals("0613865838", $formattedPhoneNumber4);
    }

    public function testMobilePhoneNumber(){
        $phone1 = "0613865838";
        $isMobile1 = $this->formatService->mobilePhoneNumber($phone1);
        $this->assertNotNull($isMobile1);

        $phone2 = "0713865838";
        $isMobile2 = $this->formatService->mobilePhoneNumber($phone2);
        $this->assertNotNull($isMobile2);

        $phone3 = "0213865838";
        $isMobile3 = $this->formatService->mobilePhoneNumber($phone3);
        $this->assertNull($isMobile3);
    }

}