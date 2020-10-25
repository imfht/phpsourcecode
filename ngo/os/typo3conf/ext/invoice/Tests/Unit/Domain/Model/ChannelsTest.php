<?php
namespace Jykj\Invoice\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Shichang Yang <yangshichang@ngoos.org>
 */
class ChannelsTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Invoice\Domain\Model\Channels
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Invoice\Domain\Model\Channels();
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
    public function getSpaer3ReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSpaer3()
        );
    }

    /**
     * @test
     */
    public function setSpaer3ForStringSetsSpaer3()
    {
        $this->subject->setSpaer3('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'spaer3',
            $this->subject
        );
    }
}
