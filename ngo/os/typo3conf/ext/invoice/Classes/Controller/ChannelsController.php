<?php
namespace Jykj\Invoice\Controller;


/***
 *
 * This file is part of the "发票管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * ChannelsController
 */
class ChannelsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * channelsRepository
     * 
     * @var \Jykj\Invoice\Domain\Repository\ChannelsRepository
     * @inject
     */
    protected $channelsRepository = null;

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $channels = $this->channelsRepository->findAll();
        $this->view->assign('channels', $channels);
    }

    /**
     * action show
     * 
     * @param \Jykj\Invoice\Domain\Model\Channels $channels
     * @return void
     */
    public function showAction(\Jykj\Invoice\Domain\Model\Channels $channels)
    {
        $this->view->assign('channels', $channels);
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
     * @param \Jykj\Invoice\Domain\Model\Channels $newChannels
     * @return void
     */
    public function createAction(\Jykj\Invoice\Domain\Model\Channels $newChannels)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->channelsRepository->add($newChannels);
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Invoice\Domain\Model\Channels $channels
     * @ignorevalidation $channels
     * @return void
     */
    public function editAction(\Jykj\Invoice\Domain\Model\Channels $channels)
    {
        $this->view->assign('channels', $channels);
    }

    /**
     * action update
     * 
     * @param \Jykj\Invoice\Domain\Model\Channels $channels
     * @return void
     */
    public function updateAction(\Jykj\Invoice\Domain\Model\Channels $channels)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->channelsRepository->update($channels);
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Invoice\Domain\Model\Channels $channels
     * @return void
     */
    public function deleteAction(\Jykj\Invoice\Domain\Model\Channels $channels)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->channelsRepository->remove($channels);
        $this->redirect('list');
    }
}
