<?php
namespace Jykj\Teamlist\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;

/***
 *
 * This file is part of the "团队列表" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Yong Hui <huiyong@ngoos.org>, Jykj
 *
 ***/
/**
 * TeamController
 */
class TeamController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * teamRepository
     * 
     * @var \Jykj\Teamlist\Domain\Repository\TeamRepository
     * @inject
     */
    protected $teamRepository = NULL;

    /**
     * action list
     * 前端显示
     * 
     * @return void
     */
    public function listAction()
    {
        $teams = $this->teamRepository->findItemsList($keyword);
        $this->view->assign('teams', $teams);
    }

    /**
     * action show
     * 
     * @param \Jykj\Teamlist\Domain\Model\Team $team
     * @return void
     */
    public function showAction(\Jykj\Teamlist\Domain\Model\Team $team)
    {
        $this->view->assign('team', $team);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        $newTeam = new \Jykj\Teamlist\Domain\Model\Team();
        $newTeam->setOrders(0);
        $this->view->assign('newTeam', $newTeam);
    }

    /**
     * action create
     * 
     * @param \Jykj\Teamlist\Domain\Model\Team $newTeam
     * @return void
     */
    public function createAction(\Jykj\Teamlist\Domain\Model\Team $newTeam)
    {
        $this->addFlashMessage('保存成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        if (!empty($_FILES['tx_teamlist_teamwork']['name']['image'])) {
            $filename = md5(uniqid($_FILES['tx_teamlist_teamwork']['name']['image'])) . '.' . end(explode('.', $_FILES['tx_teamlist_teamwork']['name']['image']));
            if (GeneralUtility::upload_copy_move($_FILES['tx_teamlist_teamwork']['tmp_name']['image'], PATH_site . 'uploads/tx_teamlist/' . $filename)) {
                $newTeam->setImage($filename);
            }
        }
        $this->teamRepository->add($newTeam);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('listback');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Teamlist\Domain\Model\Team $team
     * @ignorevalidation $team
     * @return void
     */
    public function editAction(\Jykj\Teamlist\Domain\Model\Team $team)
    {
        $this->view->assign('team', $team);
    }

    /**
     * action update
     * 
     * @param \Jykj\Teamlist\Domain\Model\Team $team
     * @return void
     */
    public function updateAction(\Jykj\Teamlist\Domain\Model\Team $team)
    {
        $this->addFlashMessage('更新成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);

        //上传图片
        if (!empty($_FILES['tx_teamlist_teamwork']['name']['image'])) {
            $filename = md5(uniqid($_FILES['tx_teamlist_teamwork']['name']['image'])) . '.' . end(explode('.', $_FILES['tx_teamlist_teamwork']['name']['image']));
            if (GeneralUtility::upload_copy_move($_FILES['tx_teamlist_teamwork']['tmp_name']['image'], PATH_site . 'uploads/tx_teamlist/' . $filename)) {
                $team->setImage($filename);
            }
        }
        $this->teamRepository->update($team);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('listback');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Teamlist\Domain\Model\Team $team
     * @return void
     */
    public function deleteAction(\Jykj\Teamlist\Domain\Model\Team $team)
    {
        $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->teamRepository->remove($team);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('listback');
    }

    /**
     * 业务后台
     */
    public function listbackAction()
    {

        //业务后台分页显示序号
        if ($_GET["tx_teamlist_teamwork"]["@widget_0"]["currentPage"]) {
            $page = $_GET["tx_teamlist_teamwork"]["@widget_0"]["currentPage"];
        } else {
            $page = 1;
        }
        $this->view->assign('page', $page);

        //获得输入框的值
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';

        //查询
        $teams = $this->teamRepository->findItemsList($keyword);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('teams', $teams);
    }

    /**
     * 多选删除
     */
    public function multideleteAction()
    {
        //多选删除
        $list = $this->request->hasArgument('datas') ? $this->request->getArgument('datas') : [];
        if ($list['items']) {
            //接收到格式 1,2,的形式，需要去掉最后一位
            $item = substr($list['items'], 0, -1);
            
            $iRet=$this->teamRepository->deleteByUidstring($item);
            if($iRet>0){
                $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
                //刷新前台缓存
                GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
                $this->redirect('listback');
            }else{
                $this->addFlashMessage('删除失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
                $this->redirect('listback');
            }
        }
        $this->addFlashMessage('没有可删除的对象！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirect('listback');
    }
}
