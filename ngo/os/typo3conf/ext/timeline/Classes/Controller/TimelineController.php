<?php
namespace Jykj\Timeline\Controller;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 WHB <wanghonbin@ngoos.org>, 极益科技
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Cache\CacheManager;
/**
 * TimelineController
 */
class TimelineController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * timelineRepository
     *
     * @var \Jykj\Timeline\Domain\Repository\TimelineRepository
     * @inject
     */
    protected $timelineRepository = NULL;
    
    public function initializeAction (){
        if($this->request->hasArgument('timeline')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('timeline')->getPropertyMappingConfiguration();
            //时间类型修改
            $propertyMappingConfiguration->forProperty('eventdate')->setTypeConverterOption('TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter', \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d' );
        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $keyword = $this->request->hasArgument("keyword")?$this->request->getArgument("keyword"):'';
        $timelines = $this->timelineRepository->findAlls($keyword);
        $this->view->assign('timelines', $timelines);
        
        if($_GET["tx_timeline_timeline"]["@widget_0"]["currentPage"]){
            $page=$_GET["tx_timeline_timeline"]["@widget_0"]["currentPage"];
        }else{
            $page=1;
        }
        $this->view->assign('page', $page);
        $this->view->assign('keyword', $keyword);
    }
    
    /**
     * action show
     *
     * @param \Jykj\Timeline\Domain\Model\Timeline $timeline
     * @return void
     */
    public function showAction(\Jykj\Timeline\Domain\Model\Timeline $timeline)
    {
        $this->view->assign('timeline', $timeline);
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
     * @param \Jykj\Timeline\Domain\Model\Timeline $timeline
     * @return void
     */
    public function createAction(\Jykj\Timeline\Domain\Model\Timeline $timeline)
    {
        $this->addFlashMessage('保存成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->timelineRepository->add($timeline);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
    
    /**
     * action edit
     *
     * @param \Jykj\Timeline\Domain\Model\Timeline $timeline
     * @ignorevalidation $timeline
     * @return void
     */
    public function editAction(\Jykj\Timeline\Domain\Model\Timeline $timeline)
    {
        $this->view->assign('timeline', $timeline);
    }
    
    /**
     * action update
     *
     * @param \Jykj\Timeline\Domain\Model\Timeline $timeline
     * @return void
     */
    public function updateAction(\Jykj\Timeline\Domain\Model\Timeline $timeline)
    {
        $this->addFlashMessage('修改成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->timelineRepository->update($timeline);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
    
    /**
     * action delete
     *
     * @param \Jykj\Timeline\Domain\Model\Timeline $timeline
     * @return void
     */
    public function deleteAction(\Jykj\Timeline\Domain\Model\Timeline $timeline)
    {
        $this->addFlashMessage('删除成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->timelineRepository->remove($timeline);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
    
    /**
     * action multedelete
     *
     * @return void
     */
    public function multedeleteAction()
    {
        $items = $this->request->hasArgument('datas') ? $this->request->getArgument('datas') : array();
        if($items['items']){
            $item =  substr($items['items'], 0, strlen($items['items']) - 1);
            
            $iRet=$this->timelineRepository->deleteByUidstring($item);
            if($iRet>0){
                $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
                //刷新前台缓存
                GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
                $this->redirect('list');
            }else{
                $this->addFlashMessage('删除失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
                $this->redirect('list');
            }
        }
        $this->addFlashMessage('没有可删除对象', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirect('list');
    }
    
    /**
     * action qtlist
     *
     * @return void
     */
    public function qtlistAction()
    {
        $timelines = $this->timelineRepository->findAlls('');
        $this->view->assign('timelines', $timelines);
    }
    
    /**
     * action spareajax
     *
     * @return void
     */
    public function spareajaxAction()
    {
        
    }

}