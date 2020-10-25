<?php
namespace Jykj\Echarts\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin816@gmail.com>
 */
class EchartsTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Echarts\Domain\Model\Echarts
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Echarts\Domain\Model\Echarts();
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
    public function getThemeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTheme()
        );
    }

    /**
     * @test
     */
    public function setThemeForStringSetsTheme()
    {
        $this->subject->setTheme('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'theme',
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
    public function getCodeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getCode()
        );
    }

    /**
     * @test
     */
    public function setCodeForStringSetsCode()
    {
        $this->subject->setCode('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'code',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDatasReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDatas()
        );
    }

    /**
     * @test
     */
    public function setDatasForStringSetsDatas()
    {
        $this->subject->setDatas('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'datas',
            $this->subject
        );
    }
}
