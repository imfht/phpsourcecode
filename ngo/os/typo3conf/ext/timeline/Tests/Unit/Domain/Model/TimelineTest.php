<?php
namespace Jykj\Timeline\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author WHB <wanghonbin@ngoos.org>
 */
class TimelineTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Timeline\Domain\Model\Timeline
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Timeline\Domain\Model\Timeline();
    }

    protected function tearDown()
    {
        parent::tearDown();
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
    public function getEventdateReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getEventdate()
        );
    }

    /**
     * @test
     */
    public function setEventdateForDateTimeSetsEventdate()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setEventdate($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'eventdate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getBodytextReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getBodytext()
        );
    }

    /**
     * @test
     */
    public function setBodytextForStringSetsBodytext()
    {
        $this->subject->setBodytext('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'bodytext',
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
}
