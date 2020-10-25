<?php
namespace Jykj\Dicts\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Shichang Yang <yangshichang@ngoos.org>
 */
class AreaControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Dicts\Controller\AreaController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Dicts\Controller\AreaController::class)
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
    public function listActionFetchesAllAreasFromRepositoryAndAssignsThemToView()
    {

        $allAreas = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $areaRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\AreaRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $areaRepository->expects(self::once())->method('findAll')->will(self::returnValue($allAreas));
        $this->inject($this->subject, 'areaRepository', $areaRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('areas', $allAreas);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenAreaToView()
    {
        $area = new \Jykj\Dicts\Domain\Model\Area();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('area', $area);

        $this->subject->showAction($area);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenAreaToAreaRepository()
    {
        $area = new \Jykj\Dicts\Domain\Model\Area();

        $areaRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\AreaRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $areaRepository->expects(self::once())->method('add')->with($area);
        $this->inject($this->subject, 'areaRepository', $areaRepository);

        $this->subject->createAction($area);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenAreaToView()
    {
        $area = new \Jykj\Dicts\Domain\Model\Area();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('area', $area);

        $this->subject->editAction($area);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenAreaInAreaRepository()
    {
        $area = new \Jykj\Dicts\Domain\Model\Area();

        $areaRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\AreaRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $areaRepository->expects(self::once())->method('update')->with($area);
        $this->inject($this->subject, 'areaRepository', $areaRepository);

        $this->subject->updateAction($area);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenAreaFromAreaRepository()
    {
        $area = new \Jykj\Dicts\Domain\Model\Area();

        $areaRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\AreaRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $areaRepository->expects(self::once())->method('remove')->with($area);
        $this->inject($this->subject, 'areaRepository', $areaRepository);

        $this->subject->deleteAction($area);
    }
}
