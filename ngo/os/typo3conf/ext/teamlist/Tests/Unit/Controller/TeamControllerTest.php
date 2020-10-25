<?php
namespace Jykj\Teamlist\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Yangshichang <Yangshichang@ngoos.org>
 */
class TeamControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Teamlist\Controller\TeamController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Teamlist\Controller\TeamController::class)
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
    public function listActionFetchesAllTeamsFromRepositoryAndAssignsThemToView()
    {

        $allTeams = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $teamRepository = $this->getMockBuilder(\Jykj\Teamlist\Domain\Repository\TeamRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $teamRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTeams));
        $this->inject($this->subject, 'teamRepository', $teamRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('teams', $allTeams);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenTeamToView()
    {
        $team = new \Jykj\Teamlist\Domain\Model\Team();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('team', $team);

        $this->subject->showAction($team);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenTeamToTeamRepository()
    {
        $team = new \Jykj\Teamlist\Domain\Model\Team();

        $teamRepository = $this->getMockBuilder(\Jykj\Teamlist\Domain\Repository\TeamRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $teamRepository->expects(self::once())->method('add')->with($team);
        $this->inject($this->subject, 'teamRepository', $teamRepository);

        $this->subject->createAction($team);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenTeamToView()
    {
        $team = new \Jykj\Teamlist\Domain\Model\Team();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('team', $team);

        $this->subject->editAction($team);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenTeamInTeamRepository()
    {
        $team = new \Jykj\Teamlist\Domain\Model\Team();

        $teamRepository = $this->getMockBuilder(\Jykj\Teamlist\Domain\Repository\TeamRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $teamRepository->expects(self::once())->method('update')->with($team);
        $this->inject($this->subject, 'teamRepository', $teamRepository);

        $this->subject->updateAction($team);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenTeamFromTeamRepository()
    {
        $team = new \Jykj\Teamlist\Domain\Model\Team();

        $teamRepository = $this->getMockBuilder(\Jykj\Teamlist\Domain\Repository\TeamRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $teamRepository->expects(self::once())->method('remove')->with($team);
        $this->inject($this->subject, 'teamRepository', $teamRepository);

        $this->subject->deleteAction($team);
    }
}
