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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;

/**
 * DicttypeController
 */
class DicttypeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * dicttypeRepository
     *
     * @var \Jykj\Dicts\Domain\Repository\DicttypeRepository
     * @inject
     */
    protected $dicttypeRepository = null;
    
    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        if ($_GET['tx_dicts_dicttype']['@widget_0']['currentPage']) {
            $page = $_GET['tx_dicts_dicttype']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $dicttypes = $this->dicttypeRepository->findAlls($keyword);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('dicttypes', $dicttypes);
        $this->view->assign('page', $page);
    }
    
    /**
     * action show
     *
     * @param \Jykj\Dicts\Domain\Model\Dicttype $dicttype
     * @return void
     */
    public function showAction(\Jykj\Dicts\Domain\Model\Dicttype $dicttype)
    {
        $this->view->assign('dicttype', $dicttype);
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
        $data = $this->request->getArguments();
        
        $nameArr = $data["name"];//名称
        $remarkArr = $data["remarks"];//描述
        $sortArr = $data["sort"];//排序
        
        for($i=0;$i<count($nameArr);$i++){
            $dicttype = new \Jykj\Dicts\Domain\Model\Dicttype;
            $dicttype->setName($nameArr[$i]);
            $dicttype->setRemarks($remarkArr[$i]);
            $dicttype->setSort($sortArr[$i]);
            $this->dicttypeRepository->add($dicttype);
            $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
            $persistenceManager->persistAll();
        }
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        
        $this->addFlashMessage('字典类别新增成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->redirect('list');
    }
    
    /**
     * action edit
     *
     * @param \Jykj\Dicts\Domain\Model\Dicttype $dicttype
     * @ignorevalidation $dicttype
     * @return void
     */
    public function editAction(\Jykj\Dicts\Domain\Model\Dicttype $dicttype)
    {
        $this->view->assign('dicttype', $dicttype);
    }
    
    /**
     * action update
     *
     * @param \Jykj\Dicts\Domain\Model\Dicttype $dicttype
     * @return void
     */
    public function updateAction(\Jykj\Dicts\Domain\Model\Dicttype $dicttype)
    {
        $this->addFlashMessage('字典类别修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->dicttypeRepository->update($dicttype);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
    
    /**
     * action delete
     *
     * @param \Jykj\Dicts\Domain\Model\Dicttype $dicttype
     * @return void
     */
    public function deleteAction(\Jykj\Dicts\Domain\Model\Dicttype $dicttype)
    {
        $this->addFlashMessage('字典类别删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->dicttypeRepository->remove($dicttype);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
    
    /**
     * action interface
     *
     * @return void
     */
    public function interfaceAction()
    {
        die("正在开发中");
    }
}