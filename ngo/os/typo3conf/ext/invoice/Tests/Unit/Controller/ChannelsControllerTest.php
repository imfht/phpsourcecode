<?php
namespace Jykj\Invoice\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Shichang Yang <yangshichang@ngoos.org>
 */
class ChannelsControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Invoice\Controller\ChannelsController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Invoice\Controller\ChannelsController::class)
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
    public function listActionFetchesAllChannelssFromRepositoryAndAssignsThemToView()
    {

        $allChannelss = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channelsRepository = $this->getMockBuilder(\Jykj\Invoice\Domain\Repository\ChannelsRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $channelsRepository->expects(self::once())->method('findAll')->will(self::returnValue($allChannelss));
        $this->inject($this->subject, 'channelsRepository', $channelsRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('channelss', $allChannelss);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenChannelsToView()
    {
        $channels = new \Jykj\Invoice\Domain\Model\Channels();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('channels', $channels);

        $this->subject->showAction($channels);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenChannelsToChannelsRepository()
    {
        $channels = new \Jykj\Invoice\Domain\Model\Channels();

        $channelsRepository = $this->getMockBuilder(\Jykj\Invoice\Domain\Repository\ChannelsRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $channelsRepository->expects(self::once())->method('add')->with($channels);
        $this->inject($this->subject, 'channelsRepository', $channelsRepository);

        $this->subject->createAction($channels);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenChannelsToView()
    {
        $channels = new \Jykj\Invoice\Domain\Model\Channels();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('channels', $channels);

        $this->subject->editAction($channels);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenChannelsInChannelsRepository()
    {
        $channels = new \Jykj\Invoice\Domain\Model\Channels();

        $channelsRepository = $this->getMockBuilder(\Jykj\Invoice\Domain\Repository\ChannelsRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $channelsRepository->expects(self::once())->method('update')->with($channels);
        $this->inject($this->subject, 'channelsRepository', $channelsRepository);

        $this->subject->updateAction($channels);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenChannelsFromChannelsRepository()
    {
        $channels = new \Jykj\Invoice\Domain\Model\Channels();

        $channelsRepository = $this->getMockBuilder(\Jykj\Invoice\Domain\Repository\ChannelsRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $channelsRepository->expects(self::once())->method('remove')->with($channels);
        $this->inject($this->subject, 'channelsRepository', $channelsRepository);

        $this->subject->deleteAction($channels);
    }
}
