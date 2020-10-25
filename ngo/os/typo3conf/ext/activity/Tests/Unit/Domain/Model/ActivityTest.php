<?php
namespace Jykj\Activity\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class ActivityTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Activity\Domain\Model\Activity
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Activity\Domain\Model\Activity();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getProvinceReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getProvince()
        );
    }

    /**
     * @test
     */
    public function setProvinceForIntSetsProvince()
    {
        $this->subject->setProvince(12);

        self::assertAttributeEquals(
            12,
            'province',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCityReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function setCityForIntSetsCity()
    {
        $this->subject->setCity(12);

        self::assertAttributeEquals(
            12,
            'city',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAreaReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getArea()
        );
    }

    /**
     * @test
     */
    public function setAreaForIntSetsArea()
    {
        $this->subject->setArea(12);

        self::assertAttributeEquals(
            12,
            'area',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAddressReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getAddress()
        );
    }

    /**
     * @test
     */
    public function setAddressForStringSetsAddress()
    {
        $this->subject->setAddress('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'address',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTradReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getTrad()
        );
    }

    /**
     * @test
     */
    public function setTradForIntSetsTrad()
    {
        $this->subject->setTrad(12);

        self::assertAttributeEquals(
            12,
            'trad',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTypesReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getTypes()
        );
    }

    /**
     * @test
     */
    public function setTypesForIntSetsTypes()
    {
        $this->subject->setTypes(12);

        self::assertAttributeEquals(
            12,
            'types',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTagReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTag()
        );
    }

    /**
     * @test
     */
    public function setTagForStringSetsTag()
    {
        $this->subject->setTag('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'tag',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPeopleReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getPeople()
        );
    }

    /**
     * @test
     */
    public function setPeopleForIntSetsPeople()
    {
        $this->subject->setPeople(12);

        self::assertAttributeEquals(
            12,
            'people',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPicturesReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPictures()
        );
    }

    /**
     * @test
     */
    public function setPicturesForStringSetsPictures()
    {
        $this->subject->setPictures('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'pictures',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSttimeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getSttime()
        );
    }

    /**
     * @test
     */
    public function setSttimeForIntSetsSttime()
    {
        $this->subject->setSttime(12);

        self::assertAttributeEquals(
            12,
            'sttime',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getOvertimeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getOvertime()
        );
    }

    /**
     * @test
     */
    public function setOvertimeForIntSetsOvertime()
    {
        $this->subject->setOvertime(12);

        self::assertAttributeEquals(
            12,
            'overtime',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getIntroduceReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getIntroduce()
        );
    }

    /**
     * @test
     */
    public function setIntroduceForStringSetsIntroduce()
    {
        $this->subject->setIntroduce('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'introduce',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getContentsReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getContents()
        );
    }

    /**
     * @test
     */
    public function setContentsForStringSetsContents()
    {
        $this->subject->setContents('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'contents',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getQrcodeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getQrcode()
        );
    }

    /**
     * @test
     */
    public function setQrcodeForStringSetsQrcode()
    {
        $this->subject->setQrcode('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'qrcode',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSendstatReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getSendstat()
        );
    }

    /**
     * @test
     */
    public function setSendstatForIntSetsSendstat()
    {
        $this->subject->setSendstat(12);

        self::assertAttributeEquals(
            12,
            'sendstat',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getModeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getMode()
        );
    }

    /**
     * @test
     */
    public function setModeForIntSetsMode()
    {
        $this->subject->setMode(12);

        self::assertAttributeEquals(
            12,
            'mode',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getMoneyReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getMoney()
        );
    }

    /**
     * @test
     */
    public function setMoneyForFloatSetsMoney()
    {
        $this->subject->setMoney(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'money',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getCkstatReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getCkstat()
        );
    }

    /**
     * @test
     */
    public function setCkstatForIntSetsCkstat()
    {
        $this->subject->setCkstat(12);

        self::assertAttributeEquals(
            12,
            'ckstat',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getResultsReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getResults()
        );
    }

    /**
     * @test
     */
    public function setResultsForStringSetsResults()
    {
        $this->subject->setResults('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'results',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCheckuserReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getCheckuser()
        );
    }

    /**
     * @test
     */
    public function setCheckuserForIntSetsCheckuser()
    {
        $this->subject->setCheckuser(12);

        self::assertAttributeEquals(
            12,
            'checkuser',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSenduserReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getSenduser()
        );
    }

    /**
     * @test
     */
    public function setSenduserForIntSetsSenduser()
    {
        $this->subject->setSenduser(12);

        self::assertAttributeEquals(
            12,
            'senduser',
            $this->subject
        );
    }
}
