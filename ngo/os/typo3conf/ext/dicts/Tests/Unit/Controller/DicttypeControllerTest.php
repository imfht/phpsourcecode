<?php
namespace Jykj\Dicts\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Shichang Yang <yangshichang@ngoos.org>
 */
class DicttypeControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Dicts\Controller\DicttypeController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Dicts\Controller\DicttypeController::class)
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
    public function listActionFetchesAllDicttypesFromRepositoryAndAssignsThemToView()
    {

        $allDicttypes = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dicttypeRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DicttypeRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $dicttypeRepository->expects(self::once())->method('findAll')->will(self::returnValue($allDicttypes));
        $this->inject($this->subject, 'dicttypeRepository', $dicttypeRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('dicttypes', $allDicttypes);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenDicttypeToView()
    {
        $dicttype = new \Jykj\Dicts\Domain\Model\Dicttype();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('dicttype', $dicttype);

        $this->subject->showAction($dicttype);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenDicttypeToDicttypeRepository()
    {
        $dicttype = new \Jykj\Dicts\Domain\Model\Dicttype();

        $dicttypeRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DicttypeRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $dicttypeRepository->expects(self::once())->method('add')->with($dicttype);
        $this->inject($this->subject, 'dicttypeRepository', $dicttypeRepository);

        $this->subject->createAction($dicttype);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenDicttypeToView()
    {
        $dicttype = new \Jykj\Dicts\Domain\Model\Dicttype();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('dicttype', $dicttype);

        $this->subject->editAction($dicttype);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenDicttypeInDicttypeRepository()
    {
        $dicttype = new \Jykj\Dicts\Domain\Model\Dicttype();

        $dicttypeRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DicttypeRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $dicttypeRepository->expects(self::once())->method('update')->with($dicttype);
        $this->inject($this->subject, 'dicttypeRepository', $dicttypeRepository);

        $this->subject->updateAction($dicttype);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenDicttypeFromDicttypeRepository()
    {
        $dicttype = new \Jykj\Dicts\Domain\Model\Dicttype();

        $dicttypeRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DicttypeRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $dicttypeRepository->expects(self::once())->method('remove')->with($dicttype);
        $this->inject($this->subject, 'dicttypeRepository', $dicttypeRepository);

        $this->subject->deleteAction($dicttype);
    }
}
