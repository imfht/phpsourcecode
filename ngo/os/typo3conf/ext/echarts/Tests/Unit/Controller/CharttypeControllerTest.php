<?php
namespace Jykj\Echarts\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin816@gmail.com>
 */
class CharttypeControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Echarts\Controller\CharttypeController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Echarts\Controller\CharttypeController::class)
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
    public function listActionFetchesAllCharttypesFromRepositoryAndAssignsThemToView()
    {

        $allCharttypes = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $charttypeRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\CharttypeRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $charttypeRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCharttypes));
        $this->inject($this->subject, 'charttypeRepository', $charttypeRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('charttypes', $allCharttypes);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCharttypeToView()
    {
        $charttype = new \Jykj\Echarts\Domain\Model\Charttype();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('charttype', $charttype);

        $this->subject->showAction($charttype);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenCharttypeToCharttypeRepository()
    {
        $charttype = new \Jykj\Echarts\Domain\Model\Charttype();

        $charttypeRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\CharttypeRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $charttypeRepository->expects(self::once())->method('add')->with($charttype);
        $this->inject($this->subject, 'charttypeRepository', $charttypeRepository);

        $this->subject->createAction($charttype);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenCharttypeToView()
    {
        $charttype = new \Jykj\Echarts\Domain\Model\Charttype();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('charttype', $charttype);

        $this->subject->editAction($charttype);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenCharttypeInCharttypeRepository()
    {
        $charttype = new \Jykj\Echarts\Domain\Model\Charttype();

        $charttypeRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\CharttypeRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $charttypeRepository->expects(self::once())->method('update')->with($charttype);
        $this->inject($this->subject, 'charttypeRepository', $charttypeRepository);

        $this->subject->updateAction($charttype);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenCharttypeFromCharttypeRepository()
    {
        $charttype = new \Jykj\Echarts\Domain\Model\Charttype();

        $charttypeRepository = $this->getMockBuilder(\Jykj\Echarts\Domain\Repository\CharttypeRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $charttypeRepository->expects(self::once())->method('remove')->with($charttype);
        $this->inject($this->subject, 'charttypeRepository', $charttypeRepository);

        $this->subject->deleteAction($charttype);
    }
}
