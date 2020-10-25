<?php
namespace Jykj\CaseTab\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author 杨世昌 <yangshichang@ngoos.org>
 */
class CasetabTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\CaseTab\Domain\Model\Casetab
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\CaseTab\Domain\Model\Casetab();
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
    public function getContentReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getContent()
        );
    }

    /**
     * @test
     */
    public function setContentForStringSetsContent()
    {
        $this->subject->setContent('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'content',
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
    public function getDatetimeReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getDatetime()
        );
    }

    /**
     * @test
     */
    public function setDatetimeForDateTimeSetsDatetime()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setDatetime($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'datetime',
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
    public function getSpare6ReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSpare6()
        );
    }

    /**
     * @test
     */
    public function setSpare6ForStringSetsSpare6()
    {
        $this->subject->setSpare6('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'spare6',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getProductReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getProduct()
        );
    }

    /**
     * @test
     */
    public function setProductForStringSetsProduct()
    {
        $this->subject->setProduct('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'product',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getLabelsReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getLabels()
        );
    }

    /**
     * @test
     */
    public function setLabelsForStringSetsLabels()
    {
        $this->subject->setLabels('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'labels',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getHitsReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getHits()
        );
    }

    /**
     * @test
     */
    public function setHitsForIntSetsHits()
    {
        $this->subject->setHits(12);

        self::assertAttributeEquals(
            12,
            'hits',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCasetypeReturnsInitialValueForCasetype()
    {
        self::assertEquals(
            null,
            $this->subject->getCasetype()
        );
    }

    /**
     * @test
     */
    public function setCasetypeForCasetypeSetsCasetype()
    {
        $casetypeFixture = new \Jykj\CaseTab\Domain\Model\Casetype();
        $this->subject->setCasetype($casetypeFixture);

        self::assertAttributeEquals(
            $casetypeFixture,
            'casetype',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getIndustryReturnsInitialValueForDictitem()
    {
    }

    /**
     * @test
     */
    public function setIndustryForDictitemSetsIndustry()
    {
    }
}
