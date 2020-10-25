<?php
namespace Jykj\CaseTab\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author 杨世昌 <yangshichang@ngoos.org>
 */
class CasetabControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\CaseTab\Controller\CasetabController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\CaseTab\Controller\CasetabController::class)
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
    public function listActionFetchesAllCasetabsFromRepositoryAndAssignsThemToView()
    {

        $allCasetabs = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $casetabRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetabRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $casetabRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCasetabs));
        $this->inject($this->subject, 'casetabRepository', $casetabRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('casetabs', $allCasetabs);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCasetabToView()
    {
        $casetab = new \Jykj\CaseTab\Domain\Model\Casetab();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('casetab', $casetab);

        $this->subject->showAction($casetab);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenCasetabToCasetabRepository()
    {
        $casetab = new \Jykj\CaseTab\Domain\Model\Casetab();

        $casetabRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetabRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $casetabRepository->expects(self::once())->method('add')->with($casetab);
        $this->inject($this->subject, 'casetabRepository', $casetabRepository);

        $this->subject->createAction($casetab);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenCasetabToView()
    {
        $casetab = new \Jykj\CaseTab\Domain\Model\Casetab();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('casetab', $casetab);

        $this->subject->editAction($casetab);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenCasetabInCasetabRepository()
    {
        $casetab = new \Jykj\CaseTab\Domain\Model\Casetab();

        $casetabRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetabRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $casetabRepository->expects(self::once())->method('update')->with($casetab);
        $this->inject($this->subject, 'casetabRepository', $casetabRepository);

        $this->subject->updateAction($casetab);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenCasetabFromCasetabRepository()
    {
        $casetab = new \Jykj\CaseTab\Domain\Model\Casetab();

        $casetabRepository = $this->getMockBuilder(\Jykj\CaseTab\Domain\Repository\CasetabRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $casetabRepository->expects(self::once())->method('remove')->with($casetab);
        $this->inject($this->subject, 'casetabRepository', $casetabRepository);

        $this->subject->deleteAction($casetab);
    }
}
