<?php
namespace Jykj\PhotoAlbum\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class PhotosTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\PhotoAlbum\Domain\Model\Photos
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Jykj\PhotoAlbum\Domain\Model\Photos();
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
}
