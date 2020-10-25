<?php
namespace Jykj\Siteconfig\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author yangshichang <yangshichang@ngoos.org>
 */
class ConfigControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Jykj\Siteconfig\Controller\ConfigController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Jykj\Siteconfig\Controller\ConfigController::class)
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
    public function listActionFetchesAllConfigsFromRepositoryAndAssignsThemToView()
    {

        $allConfigs = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository = $this->getMockBuilder(\Jykj\Siteconfig\Domain\Repository\ConfigRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $configRepository->expects(self::once())->method('findAll')->will(self::returnValue($allConfigs));
        $this->inject($this->subject, 'configRepository', $configRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('configs', $allConfigs);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenConfigToView()
    {
        $config = new \Jykj\Siteconfig\Domain\Model\Config();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('config', $config);

        $this->subject->showAction($config);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenConfigToConfigRepository()
    {
        $config = new \Jykj\Siteconfig\Domain\Model\Config();

        $configRepository = $this->getMockBuilder(\Jykj\Siteconfig\Domain\Repository\ConfigRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository->expects(self::once())->method('add')->with($config);
        $this->inject($this->subject, 'configRepository', $configRepository);

        $this->subject->createAction($config);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenConfigToView()
    {
        $config = new \Jykj\Siteconfig\Domain\Model\Config();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('config', $config);

        $this->subject->editAction($config);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenConfigInConfigRepository()
    {
        $config = new \Jykj\Siteconfig\Domain\Model\Config();

        $configRepository = $this->getMockBuilder(\Jykj\Siteconfig\Domain\Repository\ConfigRepository::class)
            ->setMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository->expects(self::once())->method('update')->with($config);
        $this->inject($this->subject, 'configRepository', $configRepository);

        $this->subject->updateAction($config);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenConfigFromConfigRepository()
    {
        $config = new \Jykj\Siteconfig\Domain\Model\Config();

        $configRepository = $this->getMockBuilder(\Jykj\Siteconfig\Domain\Repository\ConfigRepository::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository->expects(self::once())->method('remove')->with($config);
        $this->inject($this->subject, 'configRepository', $configRepository);

        $this->subject->deleteAction($config);
    }
}
