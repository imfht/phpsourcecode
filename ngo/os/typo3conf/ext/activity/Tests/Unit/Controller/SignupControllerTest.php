<?php
namespace Jykj\Activity\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class SignupControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Activity\Controller\SignupController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Activity\Controller\SignupController::class)
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
    public function listActionFetchesAllSignupsFromRepositoryAndAssignsThemToView()
    {

        $allSignups = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $signupRepository = $this->getMockBuilder(\Jykj\Activity\Domain\Repository\SignupRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $signupRepository->expects(self::once())->method('findAll')->will(self::returnValue($allSignups));
        $this->inject($this->subject, 'signupRepository', $signupRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('signups', $allSignups);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenSignupToView()
    {
        $signup = new \Jykj\Activity\Domain\Model\Signup();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('signup', $signup);

        $this->subject->showAction($signup);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenSignupToSignupRepository()
    {
        $signup = new \Jykj\Activity\Domain\Model\Signup();

        $signupRepository = $this->getMockBuilder(\Jykj\Activity\Domain\Repository\SignupRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $signupRepository->expects(self::once())->method('add')->with($signup);
        $this->inject($this->subject, 'signupRepository', $signupRepository);

        $this->subject->createAction($signup);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenSignupToView()
    {
        $signup = new \Jykj\Activity\Domain\Model\Signup();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('signup', $signup);

        $this->subject->editAction($signup);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenSignupInSignupRepository()
    {
        $signup = new \Jykj\Activity\Domain\Model\Signup();

        $signupRepository = $this->getMockBuilder(\Jykj\Activity\Domain\Repository\SignupRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $signupRepository->expects(self::once())->method('update')->with($signup);
        $this->inject($this->subject, 'signupRepository', $signupRepository);

        $this->subject->updateAction($signup);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenSignupFromSignupRepository()
    {
        $signup = new \Jykj\Activity\Domain\Model\Signup();

        $signupRepository = $this->getMockBuilder(\Jykj\Activity\Domain\Repository\SignupRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $signupRepository->expects(self::once())->method('remove')->with($signup);
        $this->inject($this->subject, 'signupRepository', $signupRepository);

        $this->subject->deleteAction($signup);
    }
}
