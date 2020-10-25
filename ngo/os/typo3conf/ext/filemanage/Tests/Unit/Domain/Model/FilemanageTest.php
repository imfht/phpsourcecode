<?php
namespace Jykj\Filemanage\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class FilemanageTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Filemanage\Domain\Model\Filemanage
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\Filemanage\Domain\Model\Filemanage();
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
    public function getFilepathReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getFilepath()
        );
    }

    /**
     * @test
     */
    public function setFilepathForStringSetsFilepath()
    {
        $this->subject->setFilepath('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'filepath',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSenddateReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSenddate()
        );
    }

    /**
     * @test
     */
    public function setSenddateForStringSetsSenddate()
    {
        $this->subject->setSenddate('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'senddate',
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
    public function getFiletypesReturnsInitialValueForFiletypes()
    {
        self::assertEquals(
            null,
            $this->subject->getFiletypes()
        );
    }

    /**
     * @test
     */
    public function setFiletypesForFiletypesSetsFiletypes()
    {
        $filetypesFixture = new \Jykj\Filemanage\Domain\Model\Filetypes();
        $this->subject->setFiletypes($filetypesFixture);

        self::assertAttributeEquals(
            $filetypesFixture,
            'filetypes',
            $this->subject
        );
    }
}
