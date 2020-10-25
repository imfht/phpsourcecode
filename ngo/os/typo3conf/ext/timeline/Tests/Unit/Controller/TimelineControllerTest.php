<?php
namespace Jykj\Timeline\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author WHB <wanghonbin@ngoos.org>
 */
class TimelineControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Timeline\Controller\TimelineController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Timeline\Controller\TimelineController::class)
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
    public function listActionFetchesAllTimelinesFromRepositoryAndAssignsThemToView()
    {

        $allTimelines = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $timelineRepository = $this->getMockBuilder(\Jykj\Timeline\Domain\Repository\TimelineRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $timelineRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTimelines));
        $this->inject($this->subject, 'timelineRepository', $timelineRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('timelines', $allTimelines);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenTimelineToView()
    {
        $timeline = new \Jykj\Timeline\Domain\Model\Timeline();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('timeline', $timeline);

        $this->subject->showAction($timeline);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenTimelineToTimelineRepository()
    {
        $timeline = new \Jykj\Timeline\Domain\Model\Timeline();

        $timelineRepository = $this->getMockBuilder(\Jykj\Timeline\Domain\Repository\TimelineRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $timelineRepository->expects(self::once())->method('add')->with($timeline);
        $this->inject($this->subject, 'timelineRepository', $timelineRepository);

        $this->subject->createAction($timeline);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenTimelineToView()
    {
        $timeline = new \Jykj\Timeline\Domain\Model\Timeline();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('timeline', $timeline);

        $this->subject->editAction($timeline);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenTimelineInTimelineRepository()
    {
        $timeline = new \Jykj\Timeline\Domain\Model\Timeline();

        $timelineRepository = $this->getMockBuilder(\Jykj\Timeline\Domain\Repository\TimelineRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $timelineRepository->expects(self::once())->method('update')->with($timeline);
        $this->inject($this->subject, 'timelineRepository', $timelineRepository);

        $this->subject->updateAction($timeline);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenTimelineFromTimelineRepository()
    {
        $timeline = new \Jykj\Timeline\Domain\Model\Timeline();

        $timelineRepository = $this->getMockBuilder(\Jykj\Timeline\Domain\Repository\TimelineRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $timelineRepository->expects(self::once())->method('remove')->with($timeline);
        $this->inject($this->subject, 'timelineRepository', $timelineRepository);

        $this->subject->deleteAction($timeline);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenTimelineFromTimelineRepository()
    {
        $timeline = new \Jykj\Timeline\Domain\Model\Timeline();

        $timelineRepository = $this->getMockBuilder(\Jykj\Timeline\Domain\Repository\TimelineRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $timelineRepository->expects(self::once())->method('remove')->with($timeline);
        $this->inject($this->subject, 'timelineRepository', $timelineRepository);

        $this->subject->deleteAction($timeline);
    }
}
