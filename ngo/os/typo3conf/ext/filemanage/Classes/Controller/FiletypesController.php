<?php
namespace Jykj\Filemanage\Controller;


/***
 *
 * This file is part of the "文件管理系统" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * FiletypesController
 */
class FiletypesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $filetypes = $this->filetypesRepository->findAll();
        $this->view->assign('filetypes', $filetypes);
    }

    /**
     * action show
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filetypes $filetypes
     * @return void
     */
    public function showAction(\Jykj\Filemanage\Domain\Model\Filetypes $filetypes)
    {
        $this->view->assign('filetypes', $filetypes);
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
     * @param \Jykj\Filemanage\Domain\Model\Filetypes $newFiletypes
     * @return void
     */
    public function createAction(\Jykj\Filemanage\Domain\Model\Filetypes $newFiletypes)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->filetypesRepository->add($newFiletypes);
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filetypes $filetypes
     * @ignorevalidation $filetypes
     * @return void
     */
    public function editAction(\Jykj\Filemanage\Domain\Model\Filetypes $filetypes)
    {
        $this->view->assign('filetypes', $filetypes);
    }

    /**
     * action update
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filetypes $filetypes
     * @return void
     */
    public function updateAction(\Jykj\Filemanage\Domain\Model\Filetypes $filetypes)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->filetypesRepository->update($filetypes);
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filetypes $filetypes
     * @return void
     */
    public function deleteAction(\Jykj\Filemanage\Domain\Model\Filetypes $filetypes)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->filetypesRepository->remove($filetypes);
        $this->redirect('list');
    }
}
