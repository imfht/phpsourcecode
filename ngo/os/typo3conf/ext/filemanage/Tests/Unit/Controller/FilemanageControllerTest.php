<?php
namespace Jykj\Filemanage\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class FilemanageControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Filemanage\Controller\FilemanageController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Filemanage\Controller\FilemanageController::class)
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
    public function listActionFetchesAllFilemanagesFromRepositoryAndAssignsThemToView()
    {

        $allFilemanages = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filemanageRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FilemanageRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $filemanageRepository->expects(self::once())->method('findAll')->will(self::returnValue($allFilemanages));
        $this->inject($this->subject, 'filemanageRepository', $filemanageRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('filemanages', $allFilemanages);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenFilemanageToView()
    {
        $filemanage = new \Jykj\Filemanage\Domain\Model\Filemanage();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('filemanage', $filemanage);

        $this->subject->showAction($filemanage);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenFilemanageToFilemanageRepository()
    {
        $filemanage = new \Jykj\Filemanage\Domain\Model\Filemanage();

        $filemanageRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FilemanageRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $filemanageRepository->expects(self::once())->method('add')->with($filemanage);
        $this->inject($this->subject, 'filemanageRepository', $filemanageRepository);

        $this->subject->createAction($filemanage);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenFilemanageToView()
    {
        $filemanage = new \Jykj\Filemanage\Domain\Model\Filemanage();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('filemanage', $filemanage);

        $this->subject->editAction($filemanage);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenFilemanageInFilemanageRepository()
    {
        $filemanage = new \Jykj\Filemanage\Domain\Model\Filemanage();

        $filemanageRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FilemanageRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $filemanageRepository->expects(self::once())->method('update')->with($filemanage);
        $this->inject($this->subject, 'filemanageRepository', $filemanageRepository);

        $this->subject->updateAction($filemanage);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenFilemanageFromFilemanageRepository()
    {
        $filemanage = new \Jykj\Filemanage\Domain\Model\Filemanage();

        $filemanageRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FilemanageRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $filemanageRepository->expects(self::once())->method('remove')->with($filemanage);
        $this->inject($this->subject, 'filemanageRepository', $filemanageRepository);

        $this->subject->deleteAction($filemanage);
    }
}
