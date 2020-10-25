<?php
namespace Jykj\Dicts\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Shichang Yang <yangshichang@ngoos.org>
 */
class DictitemControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Dicts\Controller\DictitemController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Dicts\Controller\DictitemController::class)
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
    public function listActionFetchesAllDictitemsFromRepositoryAndAssignsThemToView()
    {

        $allDictitems = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dictitemRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DictitemRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $dictitemRepository->expects(self::once())->method('findAll')->will(self::returnValue($allDictitems));
        $this->inject($this->subject, 'dictitemRepository', $dictitemRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('dictitems', $allDictitems);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenDictitemToView()
    {
        $dictitem = new \Jykj\Dicts\Domain\Model\Dictitem();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('dictitem', $dictitem);

        $this->subject->showAction($dictitem);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenDictitemToDictitemRepository()
    {
        $dictitem = new \Jykj\Dicts\Domain\Model\Dictitem();

        $dictitemRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DictitemRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $dictitemRepository->expects(self::once())->method('add')->with($dictitem);
        $this->inject($this->subject, 'dictitemRepository', $dictitemRepository);

        $this->subject->createAction($dictitem);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenDictitemToView()
    {
        $dictitem = new \Jykj\Dicts\Domain\Model\Dictitem();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('dictitem', $dictitem);

        $this->subject->editAction($dictitem);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenDictitemInDictitemRepository()
    {
        $dictitem = new \Jykj\Dicts\Domain\Model\Dictitem();

        $dictitemRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DictitemRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $dictitemRepository->expects(self::once())->method('update')->with($dictitem);
        $this->inject($this->subject, 'dictitemRepository', $dictitemRepository);

        $this->subject->updateAction($dictitem);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenDictitemFromDictitemRepository()
    {
        $dictitem = new \Jykj\Dicts\Domain\Model\Dictitem();

        $dictitemRepository = $this->getMockBuilder(\Jykj\Dicts\Domain\Repository\DictitemRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $dictitemRepository->expects(self::once())->method('remove')->with($dictitem);
        $this->inject($this->subject, 'dictitemRepository', $dictitemRepository);

        $this->subject->deleteAction($dictitem);
    }
}
