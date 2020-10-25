<?php
namespace Jykj\Donation\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author 王宏彬 <wanghongbin@ngoos.org>
 */
class PayControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Donation\Controller\PayController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Donation\Controller\PayController::class)
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
    public function listActionFetchesAllPaysFromRepositoryAndAssignsThemToView()
    {

        $allPays = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $payRepository = $this->getMockBuilder(\Jykj\Donation\Domain\Repository\PayRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $payRepository->expects(self::once())->method('findAll')->will(self::returnValue($allPays));
        $this->inject($this->subject, 'payRepository', $payRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('pays', $allPays);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenPayToView()
    {
        $pay = new \Jykj\Donation\Domain\Model\Pay();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('pay', $pay);

        $this->subject->showAction($pay);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenPayToPayRepository()
    {
        $pay = new \Jykj\Donation\Domain\Model\Pay();

        $payRepository = $this->getMockBuilder(\Jykj\Donation\Domain\Repository\PayRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $payRepository->expects(self::once())->method('add')->with($pay);
        $this->inject($this->subject, 'payRepository', $payRepository);

        $this->subject->createAction($pay);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenPayToView()
    {
        $pay = new \Jykj\Donation\Domain\Model\Pay();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('pay', $pay);

        $this->subject->editAction($pay);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenPayInPayRepository()
    {
        $pay = new \Jykj\Donation\Domain\Model\Pay();

        $payRepository = $this->getMockBuilder(\Jykj\Donation\Domain\Repository\PayRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $payRepository->expects(self::once())->method('update')->with($pay);
        $this->inject($this->subject, 'payRepository', $payRepository);

        $this->subject->updateAction($pay);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenPayFromPayRepository()
    {
        $pay = new \Jykj\Donation\Domain\Model\Pay();

        $payRepository = $this->getMockBuilder(\Jykj\Donation\Domain\Repository\PayRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $payRepository->expects(self::once())->method('remove')->with($pay);
        $this->inject($this->subject, 'payRepository', $payRepository);

        $this->subject->deleteAction($pay);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenPayFromPayRepository()
    {
        $pay = new \Jykj\Donation\Domain\Model\Pay();

        $payRepository = $this->getMockBuilder(\Jykj\Donation\Domain\Repository\PayRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $payRepository->expects(self::once())->method('remove')->with($pay);
        $this->inject($this->subject, 'payRepository', $payRepository);

        $this->subject->deleteAction($pay);
    }
}
