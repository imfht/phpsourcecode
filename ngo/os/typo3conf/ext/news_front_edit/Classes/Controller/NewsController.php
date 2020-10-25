<?php
namespace TaoJiang\NewsFrontEdit\Controller;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 TaoJiang <ribeter267@gmail.com>
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
use TaoJiang\NewsFrontEdit\Property\TypeConverter\UploadedFileReferenceConverter;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Core\Cache\CacheManager;

/**
 * NewsController
 */
class NewsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
	 * @var \TaoJiang\NewsFrontEdit\Domain\Repository\NewsRepository
	 * @inject
	 */
	protected $newsRepository;
    
    
    /**
	 * @var \TaoJiang\NewsFrontEdit\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;
    
    
    /**
	 * @var \TaoJiang\NewsFrontEdit\Domain\Repository\FrontendUserRepository
	 * @inject
	 */
	protected $frontendUserRepository;

    
    
    public function initializeAction (){
        
        if($this->request->hasArgument('news')) {
            
            $propertyMappingConfiguration = $this->arguments->getArgument('news')->getPropertyMappingConfiguration();
            
            //时间类型修改
            $propertyMappingConfiguration->forProperty('datetime')->setTypeConverterOption('TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter', \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d H:i' );

            //视频上传
            //$propertyMappingConfiguration->forProperty('falMedia.0')->setTypeConverterOptions(
             //   'TaoJiang\NewsFrontEdit\Property\TypeConverter\UploadedFileReferenceConverter',
            //    array(
             //       UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => 'flv,mp4',
             //       UploadedFileReferenceConverter::CONFIGURATION_REPLACE_RESOURCE => TRUE,
             //   )
            //);
        }
    }
    
    

	/**
	 * action list
	 * 
	 * @return void
	 */
	public function listAction() {
    
		if($_GET["tx_newsfrontedit_news"]["@widget_0"]["currentPage"]){
        	$page=$_GET["tx_newsfrontedit_news"]["@widget_0"]["currentPage"];
        }else{
        	$page=1;
        }
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $newss = $this->newsRepository->findAllOrdering($this->settings['categories'], $keyword);
        
        $this->view->assign('keyword', $keyword);
		$this->view->assign('newss', $newss);
        $this->view->assign('pageUid', $GLOBALS['TSFE']->id);
        $this->view->assign('page', $page);
	}

	/**
	 * action new
	 * 
	 * @param \TaoJiang\NewsFrontEdit\Domain\Model\News $newNews
	 * @ignorevalidation $newNews
	 * @return void
	 */
	public function newAction(\TaoJiang\NewsFrontEdit\Domain\Model\News $news=NULL) {

        if(empty($news)){
            $news = new \TaoJiang\NewsFrontEdit\Domain\Model\News();
            $news->setDatetime(new \DateTime());
            $news->setAuthor($GLOBALS['TSFE']->fe_user->user['name']);
        }
		$this->view->assign('news', $news);
        $this->view->assign('categories',$this->categoryRepository->findByUids($this->settings['categories']));
	}

	/**
	 * action create
	 * 
	 * @param \TaoJiang\NewsFrontEdit\Domain\Model\News $news
	 * @return void
	 */
	public function createAction(\TaoJiang\NewsFrontEdit\Domain\Model\News $news) {
        $this->addFlashMessage('保存成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        if (!empty($_FILES['tx_newsfrontedit_news']['name']['imgpath'])) {
        	if(!is_dir(PATH_site.'uploads/tx_news/titles/')){
	            mkdir(PATH_site.'uploads/tx_news/titles/', 0777);
	        }
        	$filename = md5(uniqid($_FILES['tx_newsfrontedit_news']['name']['imgpath'])).'.'.end(explode('.', $_FILES['tx_newsfrontedit_news']['name']['imgpath']));
        	if(GeneralUtility::upload_copy_move($_FILES['tx_newsfrontedit_news']['tmp_name']['imgpath'], PATH_site.'uploads/tx_news/titles/'.$filename)){
        		$news->setImgpath($filename);
        	}
        }
		$news->setType(0);
		$this->newsRepository->add($news);
		//刷新前台缓存
		GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
		$this->redirect('list');
	}

	/**
	 * action edit
	 * 
	 * @param \TaoJiang\NewsFrontEdit\Domain\Model\News $news
	 * @ignorevalidation $news
	 * @return void
	 */
	public function editAction(\TaoJiang\NewsFrontEdit\Domain\Model\News $news) {
		$this->view->assign('news', $news);
        $this->view->assign('categories',$this->categoryRepository->findByUids($this->settings['categories']));
	}

	/**
	 * action update
	 * 
	 * @param \TaoJiang\NewsFrontEdit\Domain\Model\News $news
	 * @return void
	 */
	public function updateAction(\TaoJiang\NewsFrontEdit\Domain\Model\News $news) {
        $this->addFlashMessage('更新成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        if (!empty($_FILES['tx_newsfrontedit_news']['name']['imgpath'])) {
        	if(!is_dir(PATH_site.'uploads/tx_news/titles/')){
	            mkdir(PATH_site.'uploads/tx_news/titles/', 0777);
	        }
        	$filename = md5(uniqid($_FILES['tx_newsfrontedit_news']['name']['imgpath'])).'.'.end(explode('.', $_FILES['tx_newsfrontedit_news']['name']['imgpath']));
        	if(GeneralUtility::upload_copy_move($_FILES['tx_newsfrontedit_news']['tmp_name']['imgpath'], PATH_site.'uploads/tx_news/titles/'.$filename)){
        		$news->setImgpath($filename);
        	}
        }
        $news->setType(0);
        $this->newsRepository->update($news);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
		$this->redirect('list');
	}

	/**
	 * action delete
	 * 
	 * @param \TaoJiang\NewsFrontEdit\Domain\Model\News $news
	 * @return void
	 */
	public function deleteAction(\TaoJiang\NewsFrontEdit\Domain\Model\News $news) {
        $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		$this->newsRepository->remove($news);
		//刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
		$this->redirect('list');
	}
	
	
	/**
	 * 批量删除
	 * @return void
	 */
	public function multideleteAction(){
	
		$items = $this->request->hasArgument('datas') ? $this->request->getArgument('datas') : array();
		if($items['items']){
			$item =  substr($items['items'], 0, strlen($items['items']) - 1);
			$iRet = $this->newsRepository->deleteByUidstring($item);
			if($iRet>0){
			    $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			    //刷新前台缓存
			    GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
			}else{
			    $this->addFlashMessage('删除失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
            $this->redirect('list');
		}
        $this->addFlashMessage('没有可删除对象！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirect('list');
	}
    

}