<?php
namespace Jykj\Dicts\Controller;


/***
 *
 * This file is part of the "数据字典" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * AreaController
 */
class AreaController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * areaRepository
     * 
     * @var \Jykj\Dicts\Domain\Repository\AreaRepository
     * @inject
     */
    protected $areaRepository = null;

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $areas = $this->areaRepository->findAll();
        $this->view->assign('areas', $areas);
    }

    /**
     * action show
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $area
     * @return void
     */
    public function showAction(\Jykj\Dicts\Domain\Model\Area $area)
    {
        $this->view->assign('area', $area);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
    }

    /**
     * action create
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $newArea
     * @return void
     */
    public function createAction(\Jykj\Dicts\Domain\Model\Area $newArea)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->areaRepository->add($newArea);
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $area
     * @ignorevalidation $area
     * @return void
     */
    public function editAction(\Jykj\Dicts\Domain\Model\Area $area)
    {
        $this->view->assign('area', $area);
    }

    /**
     * action update
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $area
     * @return void
     */
    public function updateAction(\Jykj\Dicts\Domain\Model\Area $area)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->areaRepository->update($area);
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Dicts\Domain\Model\Area $area
     * @return void
     */
    public function deleteAction(\Jykj\Dicts\Domain\Model\Area $area)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->areaRepository->remove($area);
        $this->redirect('list');
    }

    /**
     * action interface
     * 
     * @return void
     */
    public function interfaceAction()
    {
    }
}
