<?php
namespace Jykj\CaseTab\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author 杨世昌 <yangshichang@ngoos.org>
 */
class CasetypeControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\CaseTab\Controller\CasetypeController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\CaseTab\Controller\CasetypeController::class)
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
    public function listActionFetchesAllCasetypesFromRepositoryAndAssignsThemToView()
    {

        $allCasetypes = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $casetypeRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetypeRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $casetypeRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCasetypes));
        $this->inject($this->subject, 'casetypeRepository', $casetypeRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('casetypes', $allCasetypes);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCasetypeToView()
    {
        $casetype = new \Jykj\CaseTab\Domain\Model\Casetype();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('casetype', $casetype);

        $this->subject->showAction($casetype);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenCasetypeToCasetypeRepository()
    {
        $casetype = new \Jykj\CaseTab\Domain\Model\Casetype();

        $casetypeRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetypeRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $casetypeRepository->expects(self::once())->method('add')->with($casetype);
        $this->inject($this->subject, 'casetypeRepository', $casetypeRepository);

        $this->subject->createAction($casetype);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenCasetypeToView()
    {
        $casetype = new \Jykj\CaseTab\Domain\Model\Casetype();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('casetype', $casetype);

        $this->subject->editAction($casetype);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenCasetypeInCasetypeRepository()
    {
        $casetype = new \Jykj\CaseTab\Domain\Model\Casetype();

        $casetypeRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetypeRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $casetypeRepository->expects(self::once())->method('update')->with($casetype);
        $this->inject($this->subject, 'casetypeRepository', $casetypeRepository);

        $this->subject->updateAction($casetype);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenCasetypeFromCasetypeRepository()
    {
        $casetype = new \Jykj\CaseTab\Domain\Model\Casetype();

        $casetypeRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetypeRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $casetypeRepository->expects(self::once())->method('remove')->with($casetype);
        $this->inject($this->subject, 'casetypeRepository', $casetypeRepository);

        $this->subject->deleteAction($casetype);
    }
}
