<?php
namespace Jykj\PhotoAlbum\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class AlbumControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\PhotoAlbum\Controller\AlbumController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\PhotoAlbum\Controller\AlbumController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllAlbumsFromRepositoryAndAssignsThemToView()
    {

        $allAlbums = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $albumRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\AlbumRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $albumRepository->expects(self::once())->method('findAll')->will(self::returnValue($allAlbums));
        $this->inject($this->subject, 'albumRepository', $albumRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('albums', $allAlbums);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenAlbumToView()
    {
        $album = new \Jykj\PhotoAlbum\Domain\Model\Album();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('album', $album);

        $this->subject->showAction($album);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenAlbumToAlbumRepository()
    {
        $album = new \Jykj\PhotoAlbum\Domain\Model\Album();

        $albumRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\AlbumRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $albumRepository->expects(self::once())->method('add')->with($album);
        $this->inject($this->subject, 'albumRepository', $albumRepository);

        $this->subject->createAction($album);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenAlbumToView()
    {
        $album = new \Jykj\PhotoAlbum\Domain\Model\Album();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('album', $album);

        $this->subject->editAction($album);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenAlbumInAlbumRepository()
    {
        $album = new \Jykj\PhotoAlbum\Domain\Model\Album();

        $albumRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\AlbumRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $albumRepository->expects(self::once())->method('update')->with($album);
        $this->inject($this->subject, 'albumRepository', $albumRepository);

        $this->subject->updateAction($album);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenAlbumFromAlbumRepository()
    {
        $album = new \Jykj\PhotoAlbum\Domain\Model\Album();

        $albumRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\AlbumRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $albumRepository->expects(self::once())->method('remove')->with($album);
        $this->inject($this->subject, 'albumRepository', $albumRepository);

        $this->subject->deleteAction($album);
    }
}
