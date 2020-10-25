<?php
namespace Jykj\Echarts\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin816@gmail.com>
 */
class ChartdataTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Echarts\Domain\Model\Chartdata
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Echarts\Domain\Model\Chartdata();
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
    public function getSubtitleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSubtitle()
        );
    }

    /**
     * @test
     */
    public function setSubtitleForStringSetsSubtitle()
    {
        $this->subject->setSubtitle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'subtitle',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTooltipReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTooltip()
        );
    }

    /**
     * @test
     */
    public function setTooltipForStringSetsTooltip()
    {
        $this->subject->setTooltip('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'tooltip',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getToolboxReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getToolbox()
        );
    }

    /**
     * @test
     */
    public function setToolboxForStringSetsToolbox()
    {
        $this->subject->setToolbox('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'toolbox',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getColorReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getColor()
        );
    }

    /**
     * @test
     */
    public function setColorForStringSetsColor()
    {
        $this->subject->setColor('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'color',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTextstyleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTextstyle()
        );
    }

    /**
     * @test
     */
    public function setTextstyleForStringSetsTextstyle()
    {
        $this->subject->setTextstyle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'textstyle',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCharttypeReturnsInitialValueForCharttype()
    {
        self::assertEquals(
            null,
            $this->subject->getCharttype()
        );
    }

    /**
     * @test
     */
    public function setCharttypeForCharttypeSetsCharttype()
    {
        $charttypeFixture = new \Jykj\Echarts\Domain\Model\Charttype();
        $this->subject->setCharttype($charttypeFixture);

        self::assertAttributeEquals(
            $charttypeFixture,
            'charttype',
            $this->subject
        );
    }
}
