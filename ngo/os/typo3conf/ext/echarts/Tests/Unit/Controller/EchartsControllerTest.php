<?php
namespace Jykj\Echarts\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin816@gmail.com>
 */
class EchartsControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Echarts\Controller\EchartsController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Echarts\Controller\EchartsController::class)
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
    public function listActionFetchesAllEchartssFromRepositoryAndAssignsThemToView()
    {

        $allEchartss = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $echartsRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\EchartsRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $echartsRepository->expects(self::once())->method('findAll')->will(self::returnValue($allEchartss));
        $this->inject($this->subject, 'echartsRepository', $echartsRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('echartss', $allEchartss);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenEchartsToView()
    {
        $echarts = new \Jykj\Echarts\Domain\Model\Echarts();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('echarts', $echarts);

        $this->subject->showAction($echarts);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenEchartsToEchartsRepository()
    {
        $echarts = new \Jykj\Echarts\Domain\Model\Echarts();

        $echartsRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\EchartsRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $echartsRepository->expects(self::once())->method('add')->with($echarts);
        $this->inject($this->subject, 'echartsRepository', $echartsRepository);

        $this->subject->createAction($echarts);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenEchartsToView()
    {
        $echarts = new \Jykj\Echarts\Domain\Model\Echarts();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('echarts', $echarts);

        $this->subject->editAction($echarts);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenEchartsInEchartsRepository()
    {
        $echarts = new \Jykj\Echarts\Domain\Model\Echarts();

        $echartsRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\EchartsRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $echartsRepository->expects(self::once())->method('update')->with($echarts);
        $this->inject($this->subject, 'echartsRepository', $echartsRepository);

        $this->subject->updateAction($echarts);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenEchartsFromEchartsRepository()
    {
        $echarts = new \Jykj\Echarts\Domain\Model\Echarts();

        $echartsRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\EchartsRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $echartsRepository->expects(self::once())->method('remove')->with($echarts);
        $this->inject($this->subject, 'echartsRepository', $echartsRepository);

        $this->subject->deleteAction($echarts);
    }
}
