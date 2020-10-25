<?php
namespace Jykj\Filemanage\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class FiletypesControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Filemanage\Controller\FiletypesController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Filemanage\Controller\FiletypesController::class)
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
    public function listActionFetchesAllFiletypessFromRepositoryAndAssignsThemToView()
    {

        $allFiletypess = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filetypesRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FiletypesRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $filetypesRepository->expects(self::once())->method('findAll')->will(self::returnValue($allFiletypess));
        $this->inject($this->subject, 'filetypesRepository', $filetypesRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('filetypess', $allFiletypess);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenFiletypesToView()
    {
        $filetypes = new \Jykj\Filemanage\Domain\Model\Filetypes();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('filetypes', $filetypes);

        $this->subject->showAction($filetypes);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenFiletypesToFiletypesRepository()
    {
        $filetypes = new \Jykj\Filemanage\Domain\Model\Filetypes();

        $filetypesRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FiletypesRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $filetypesRepository->expects(self::once())->method('add')->with($filetypes);
        $this->inject($this->subject, 'filetypesRepository', $filetypesRepository);

        $this->subject->createAction($filetypes);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenFiletypesToView()
    {
        $filetypes = new \Jykj\Filemanage\Domain\Model\Filetypes();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('filetypes', $filetypes);

        $this->subject->editAction($filetypes);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenFiletypesInFiletypesRepository()
    {
        $filetypes = new \Jykj\Filemanage\Domain\Model\Filetypes();

        $filetypesRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FiletypesRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $filetypesRepository->expects(self::once())->method('update')->with($filetypes);
        $this->inject($this->subject, 'filetypesRepository', $filetypesRepository);

        $this->subject->updateAction($filetypes);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenFiletypesFromFiletypesRepository()
    {
        $filetypes = new \Jykj\Filemanage\Domain\Model\Filetypes();

        $filetypesRepository = $this->getMockBuilder(\Jykj\Filemanage\Domain\Repository\FiletypesRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $filetypesRepository->expects(self::once())->method('remove')->with($filetypes);
        $this->inject($this->subject, 'filetypesRepository', $filetypesRepository);

        $this->subject->deleteAction($filetypes);
    }
}
