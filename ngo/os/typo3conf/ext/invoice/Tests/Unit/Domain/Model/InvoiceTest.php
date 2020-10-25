<?php
namespace Jykj\Invoice\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Shichang Yang <yangshichang@ngoos.org>
 */
class InvoiceTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Invoice\Domain\Model\Invoice
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Invoice\Domain\Model\Invoice();
    }

    protected function tearDown()
    {
        parent::tearDown();
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
    public function getHeaderReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getHeader()
        );
    }

    /**
     * @test
     */
    public function setHeaderForStringSetsHeader()
    {
        $this->subject->setHeader('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'header',
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
    public function getPostcodeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPostcode()
        );
    }

    /**
     * @test
     */
    public function setPostcodeForStringSetsPostcode()
    {
        $this->subject->setPostcode('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'postcode',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPeopleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPeople()
        );
    }

    /**
     * @test
     */
    public function setPeopleForStringSetsPeople()
    {
        $this->subject->setPeople('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'people',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTelphoneReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTelphone()
        );
    }

    /**
     * @test
     */
    public function setTelphoneForStringSetsTelphone()
    {
        $this->subject->setTelphone('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'telphone',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getMailReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getMail()
        );
    }

    /**
     * @test
     */
    public function setMailForStringSetsMail()
    {
        $this->subject->setMail('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'mail',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDonatetimeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getDonatetime()
        );
    }

    /**
     * @test
     */
    public function setDonatetimeForIntSetsDonatetime()
    {
        $this->subject->setDonatetime(12);

        self::assertAttributeEquals(
            12,
            'donatetime',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSpare1ReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSpare1()
        );
    }

    /**
     * @test
     */
    public function setSpare1ForStringSetsSpare1()
    {
        $this->subject->setSpare1('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'spare1',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSpare2ReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSpare2()
        );
    }

    /**
     * @test
     */
    public function setSpare2ForStringSetsSpare2()
    {
        $this->subject->setSpare2('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'spare2',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSpare3ReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSpare3()
        );
    }

    /**
     * @test
     */
    public function setSpare3ForStringSetsSpare3()
    {
        $this->subject->setSpare3('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'spare3',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSpare4ReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSpare4()
        );
    }

    /**
     * @test
     */
    public function setSpare4ForStringSetsSpare4()
    {
        $this->subject->setSpare4('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'spare4',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSpare5ReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSpare5()
        );
    }

    /**
     * @test
     */
    public function setSpare5ForStringSetsSpare5()
    {
        $this->subject->setSpare5('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'spare5',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getChannelidReturnsInitialValueForChannels()
    {
        self::assertEquals(
            null,
            $this->subject->getChannelid()
        );
    }

    /**
     * @test
     */
    public function setChannelidForChannelsSetsChannelid()
    {
        $channelidFixture = new \Jykj\Invoice\Domain\Model\Channels();
        $this->subject->setChannelid($channelidFixture);

        self::assertAttributeEquals(
            $channelidFixture,
            'channelid',
            $this->subject
        );
    }
}
