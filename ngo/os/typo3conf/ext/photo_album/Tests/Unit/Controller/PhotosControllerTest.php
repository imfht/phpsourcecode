<?php
namespace Jykj\PhotoAlbum\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class PhotosControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\PhotoAlbum\Controller\PhotosController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\PhotoAlbum\Controller\PhotosController::class)
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
    public function listActionFetchesAllPhotossFromRepositoryAndAssignsThemToView()
    {

        $allPhotoss = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $photosRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\PhotosRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $photosRepository->expects(self::once())->method('findAll')->will(self::returnValue($allPhotoss));
        $this->inject($this->subject, 'photosRepository', $photosRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('photoss', $allPhotoss);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenPhotosToView()
    {
        $photos = new \Jykj\PhotoAlbum\Domain\Model\Photos();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('photos', $photos);

        $this->subject->showAction($photos);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenPhotosToPhotosRepository()
    {
        $photos = new \Jykj\PhotoAlbum\Domain\Model\Photos();

        $photosRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\PhotosRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $photosRepository->expects(self::once())->method('add')->with($photos);
        $this->inject($this->subject, 'photosRepository', $photosRepository);

        $this->subject->createAction($photos);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenPhotosToView()
    {
        $photos = new \Jykj\PhotoAlbum\Domain\Model\Photos();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('photos', $photos);

        $this->subject->editAction($photos);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenPhotosInPhotosRepository()
    {
        $photos = new \Jykj\PhotoAlbum\Domain\Model\Photos();

        $photosRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\PhotosRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $photosRepository->expects(self::once())->method('update')->with($photos);
        $this->inject($this->subject, 'photosRepository', $photosRepository);

        $this->subject->updateAction($photos);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenPhotosFromPhotosRepository()
    {
        $photos = new \Jykj\PhotoAlbum\Domain\Model\Photos();

        $photosRepository = $this->getMockBuilder(\Jykj\PhotoAlbum\Domain\Repository\PhotosRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $photosRepository->expects(self::once())->method('remove')->with($photos);
        $this->inject($this->subject, 'photosRepository', $photosRepository);

        $this->subject->deleteAction($photos);
    }
}
