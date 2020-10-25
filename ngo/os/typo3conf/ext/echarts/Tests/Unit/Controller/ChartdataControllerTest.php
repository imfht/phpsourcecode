<?php
namespace Jykj\Echarts\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin816@gmail.com>
 */
class ChartdataControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Echarts\Controller\ChartdataController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Echarts\Controller\ChartdataController::class)
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
    public function listActionFetchesAllChartdatasFromRepositoryAndAssignsThemToView()
    {

        $allChartdatas = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $chartdataRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\ChartdataRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $chartdataRepository->expects(self::once())->method('findAll')->will(self::returnValue($allChartdatas));
        $this->inject($this->subject, 'chartdataRepository', $chartdataRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('chartdatas', $allChartdatas);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenChartdataToView()
    {
        $chartdata = new \Jykj\Echarts\Domain\Model\Chartdata();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('chartdata', $chartdata);

        $this->subject->showAction($chartdata);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenChartdataToChartdataRepository()
    {
        $chartdata = new \Jykj\Echarts\Domain\Model\Chartdata();

        $chartdataRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\ChartdataRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $chartdataRepository->expects(self::once())->method('add')->with($chartdata);
        $this->inject($this->subject, 'chartdataRepository', $chartdataRepository);

        $this->subject->createAction($chartdata);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenChartdataToView()
    {
        $chartdata = new \Jykj\Echarts\Domain\Model\Chartdata();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('chartdata', $chartdata);

        $this->subject->editAction($chartdata);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenChartdataInChartdataRepository()
    {
        $chartdata = new \Jykj\Echarts\Domain\Model\Chartdata();

        $chartdataRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\ChartdataRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $chartdataRepository->expects(self::once())->method('update')->with($chartdata);
        $this->inject($this->subject, 'chartdataRepository', $chartdataRepository);

        $this->subject->updateAction($chartdata);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenChartdataFromChartdataRepository()
    {
        $chartdata = new \Jykj\Echarts\Domain\Model\Chartdata();

        $chartdataRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\ChartdataRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $chartdataRepository->expects(self::once())->method('remove')->with($chartdata);
        $this->inject($this->subject, 'chartdataRepository', $chartdataRepository);

        $this->subject->deleteAction($chartdata);
    }
}
