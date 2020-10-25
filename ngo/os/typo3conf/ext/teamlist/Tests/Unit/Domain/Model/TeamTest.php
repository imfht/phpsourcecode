<?php
namespace Jykj\Teamlist\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Yangshichang <Yangshichang@ngoos.org>
 */
class TeamTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Teamlist\Domain\Model\Team
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Teamlist\Domain\Model\Team();
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
    public function getIntroReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getIntro()
        );
    }

    /**
     * @test
     */
    public function setIntroForStringSetsIntro()
    {
        $this->subject->setIntro('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'intro',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getImageReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getImage()
        );
    }

    /**
     * @test
     */
    public function setImageForStringSetsImage()
    {
        $this->subject->setImage('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'image',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDetailReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDetail()
        );
    }

    /**
     * @test
     */
    public function setDetailForStringSetsDetail()
    {
        $this->subject->setDetail('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'detail',
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
    public function getOrdersReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getOrders()
        );
    }

    /**
     * @test
     */
    public function setOrdersForIntSetsOrders()
    {
        $this->subject->setOrders(12);

        self::assertAttributeEquals(
            12,
            'orders',
            $this->subject
        );
    }
}
