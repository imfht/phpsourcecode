<?php
namespace Jykj\Echarts\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin816@gmail.com>
 */
class CharttypeTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Echarts\Domain\Model\Charttype
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Echarts\Domain\Model\Charttype();
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
    public function getSlugReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSlug()
        );
    }

    /**
     * @test
     */
    public function setSlugForStringSetsSlug()
    {
        $this->subject->setSlug('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'slug',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getScriptReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getScript()
        );
    }

    /**
     * @test
     */
    public function setScriptForStringSetsScript()
    {
        $this->subject->setScript('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'script',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAuthorReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getAuthor()
        );
    }

    /**
     * @test
     */
    public function setAuthorForStringSetsAuthor()
    {
        $this->subject->setAuthor('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'author',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getReviewReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getReview()
        );
    }

    /**
     * @test
     */
    public function setReviewForIntSetsReview()
    {
        $this->subject->setReview(12);

        self::assertAttributeEquals(
            12,
            'review',
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
}
