<?php
namespace Jykj\PhotoAlbum\Controller;


/***
 *
 * This file is part of the "相册管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Cache\CacheManager;


/**
 * AlbumController
 */
class AlbumController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * albumRepository
     * 
     * @var \Jykj\PhotoAlbum\Domain\Repository\AlbumRepository
     * @inject
     */
    protected $albumRepository = null;
    
    protected $iPid=78;

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        //业务后台分页显示序号
        if($_GET["tx_photoalbum_album"]["@widget_0"]["currentPage"]){
            $page=$_GET["tx_photoalbum_album"]["@widget_0"]["currentPage"];
        }else{
            $page=1;
        }
        
        $keyword=$this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $albums=$this->albumRepository->findAlls($keyword);
        
        $this->view->assign('page', $page);
        $this->view->assign('albums', $albums);
        $this->view->assign('keyword', $keyword);
    }

    /**
     * action show
     * 
     * @return void
     */
    public function showAction()
    {
        $uid=$this->request->hasArgument('album') ? $this->request->getArgument('album') : '';
        $album = $this->albumRepository->getSingleRow('sys_file_collection',$uid);
        $this->view->assign('album', $album);
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
     * @return void
     */
    public function createAction()
    {
        $this->addFlashMessage('相册创建成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        
        $title=$this->request->hasArgument('title') ? $this->request->getArgument('title') : '';
        $datetime=$this->request->hasArgument('datetime') ? $this->request->getArgument('datetime') : '';

        //先查询排序(查询最大值+1)
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_collection');
        $maxSort = $queryBuilder
        ->addSelectLiteral(
            $queryBuilder->expr()->max('sorting', 'sorting')
        )
        ->from('sys_file_collection')
        ->execute()
        ->fetchColumn(0);
        //->fetchAll();
        
        //插入数据
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $databaseConnectionForPages = $connectionPool->getConnectionForTable('sys_file_collection');
        $databaseConnectionForPages->insert(
            'sys_file_collection',
            [
                'pid' => $this->iPid,
                'tstamp' => time(),
                'crdate' => time(),
                'title' => $title,
                'type' => "folder",
                'storage' => 1,
                'folder' => "",
                'sorting' => $maxSort+1,
                'datetime'=>$datetime==""?0:strtotime($datetime)
            ]
            );
        $maxUid = (int)$databaseConnectionForPages->lastInsertId('sys_file_collection');
       
        //创建目录
        $path=PATH_site."fileadmin/albumfolder/".$maxUid."/";
        mkdir($path,0777,true);
        
        //修改记录
        $setData=array('folder' => "/albumfolder/".$maxUid."/");
        $this->albumRepository->updateRows('sys_file_collection',$setData,array('uid' => $maxUid));
        
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @ignorevalidation $album
     * @return void
     */
    public function editAction()
    {
        $uid=$this->request->hasArgument('album') ? $this->request->getArgument('album') : '';
        $album = $this->albumRepository->getSingleRow('sys_file_collection',$uid);
        $this->view->assign('album', $album);
    }

    /**
     * action update
     * 
     * @return void
     */
    public function updateAction()
    {
        $albumuid=$this->request->hasArgument('albumuid') ? $this->request->getArgument('albumuid') : 0;
        $title=$this->request->hasArgument('title') ? $this->request->getArgument('title') : '';
        $datetime=$this->request->hasArgument('datetime') ? $this->request->getArgument('datetime') : '';
        if($albumuid==0){
            $this->addFlashMessage('修改失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }else{
            $this->addFlashMessage('修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            //修改记录
            $setData=array('title' => $title,'datetime'=>$datetime==""?0:strtotime($datetime));
            $this->albumRepository->updateRows('sys_file_collection',$setData,array('uid' => $albumuid));
        }
        
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @return void
     */
    public function deleteAction()
    {
        $albumuid=$this->request->hasArgument('album') ? $this->request->getArgument('album') : 0;
        if($albumuid==0){
            $this->addFlashMessage('删除失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }else{
            $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            //修改记录
            $this->albumRepository->updateRows('sys_file_collection',array('deleted' => 1),array('uid' => $albumuid));
        }
        
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
}
