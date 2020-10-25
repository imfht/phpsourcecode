<?php
namespace Jykj\Dicts\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Shichang Yang <yangshichang@ngoos.org>
 */
class DictitemTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Dicts\Domain\Model\Dictitem();
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
    public function getShortnameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getShortname()
        );
    }

    /**
     * @test
     */
    public function setShortnameForStringSetsShortname()
    {
        $this->subject->setShortname('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'shortname',
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
    public function getRemarksReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getRemarks()
        );
    }

    /**
     * @test
     */
    public function setRemarksForStringSetsRemarks()
    {
        $this->subject->setRemarks('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'remarks',
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
    public function getSortReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getSort()
        );
    }

    /**
     * @test
     */
    public function setSortForIntSetsSort()
    {
        $this->subject->setSort(12);

        self::assertAttributeEquals(
            12,
            'sort',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSpare1ReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getSpare1()
        );
    }

    /**
     * @test
     */
    public function setSpare1ForIntSetsSpare1()
    {
        $this->subject->setSpare1(12);

        self::assertAttributeEquals(
            12,
            'spare1',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getParentuidReturnsInitialValueForDictitem()
    {
        self::assertEquals(
            null,
            $this->subject->getParentuid()
        );
    }

    /**
     * @test
     */
    public function setParentuidForDictitemSetsParentuid()
    {
        $parentuidFixture = new \Jykj\Dicts\Domain\Model\Dictitem();
        $this->subject->setParentuid($parentuidFixture);

        self::assertAttributeEquals(
            $parentuidFixture,
            'parentuid',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDicttypeReturnsInitialValueForDicttype()
    {
        self::assertEquals(
            null,
            $this->subject->getDicttype()
        );
    }

    /**
     * @test
     */
    public function setDicttypeForDicttypeSetsDicttype()
    {
        $dicttypeFixture = new \Jykj\Dicts\Domain\Model\Dicttype();
        $this->subject->setDicttype($dicttypeFixture);

        self::assertAttributeEquals(
            $dicttypeFixture,
            'dicttype',
            $this->subject
        );
    }
}
