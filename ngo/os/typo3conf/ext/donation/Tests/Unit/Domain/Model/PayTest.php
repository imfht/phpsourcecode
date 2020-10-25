<?php
namespace Jykj\Donation\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin@ngoos.org>
 */
class PayTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Donation\Domain\Model\Pay
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Donation\Domain\Model\Pay();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getCommentReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getComment()
        );
    }

    /**
     * @test
     */
    public function setCommentForStringSetsComment()
    {
        $this->subject->setComment('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'comment',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
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
    public function getEmailReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailForStringSetsEmail()
    {
        $this->subject->setEmail('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'email',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTelephoneReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTelephone()
        );
    }

    /**
     * @test
     */
    public function setTelephoneForStringSetsTelephone()
    {
        $this->subject->setTelephone('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'telephone',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getModuleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getModule()
        );
    }

    /**
     * @test
     */
    public function setModuleForStringSetsModule()
    {
        $this->subject->setModule('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'module',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDatauidReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getDatauid()
        );
    }

    /**
     * @test
     */
    public function setDatauidForIntSetsDatauid()
    {
        $this->subject->setDatauid(12);

        self::assertAttributeEquals(
            12,
            'datauid',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getUrlReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getUrl()
        );
    }

    /**
     * @test
     */
    public function setUrlForStringSetsUrl()
    {
        $this->subject->setUrl('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'url',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getOrdernumberReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getOrdernumber()
        );
    }

    /**
     * @test
     */
    public function setOrdernumberForStringSetsOrdernumber()
    {
        $this->subject->setOrdernumber('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'ordernumber',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPaymentReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPayment()
        );
    }

    /**
     * @test
     */
    public function setPaymentForStringSetsPayment()
    {
        $this->subject->setPayment('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'payment',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCertnumberReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getCertnumber()
        );
    }

    /**
     * @test
     */
    public function setCertnumberForStringSetsCertnumber()
    {
        $this->subject->setCertnumber('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'certnumber',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSpreadshareuseridReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getSpreadshareuserid()
        );
    }

    /**
     * @test
     */
    public function setSpreadshareuseridForIntSetsSpreadshareuserid()
    {
        $this->subject->setSpreadshareuserid(12);

        self::assertAttributeEquals(
            12,
            'spreadshareuserid',
            $this->subject
        );
    }
}
