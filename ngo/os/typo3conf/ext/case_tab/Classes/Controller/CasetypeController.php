<?php
namespace Jykj\CaseTab\Controller;


/***
 *
 * This file is part of the "应用案例" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 杨世昌 <yangshichang@ngoos.org>, 极益科技
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
/**
 * CasetypeController
 */
class CasetypeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * casetypeRepository
     * 
     * @var \Jykj\CaseTab\Domain\Repository\CasetypeRepository
     * @inject
     */
    protected $casetypeRepository = null;

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $casetypes = $this->casetypeRepository->findAlls($keyword);
        $this->view->assign('casetypes', $casetypes);
        if ($_GET['tx_casetab_case']['@widget_0']['currentPage']) {
            $page = $_GET['tx_casetab_case']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $this->view->assign('page', $page);
        $this->view->assign('keyword', $keyword);
    }

    /**
     * action show
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetype $casetype
     * @return void
     */
    public function showAction(\Jykj\CaseTab\Domain\Model\Casetype $casetype)
    {
        $this->view->assign('casetype', $casetype);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        $casetype = new \Jykj\CaseTab\Domain\Model\Casetype;
        
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_casetab_domain_model_casetype');
        $maxSort = $queryBuilder
        ->addSelectLiteral(
            $queryBuilder->expr()->max('sort', 'sort')
        )
        ->from('tx_casetab_domain_model_casetype')
        ->execute()
        ->fetchColumn(0);   
        $casetype->setSort(($maxSort+1));
        $this->view->assign('casetype', $casetype);
    }

    /**
     * action create
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetype $casetype
     * @return void
     */
    public function createAction(\Jykj\CaseTab\Domain\Model\Casetype $casetype)
    {
        $this->addFlashMessage('案例类型新增成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->casetypeRepository->add($casetype);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetype $casetype
     * @ignorevalidation $casetype
     * @return void
     */
    public function editAction(\Jykj\CaseTab\Domain\Model\Casetype $casetype)
    {
        $this->view->assign('casetype', $casetype);
    }

    /**
     * action update
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetype $casetype
     * @return void
     */
    public function updateAction(\Jykj\CaseTab\Domain\Model\Casetype $casetype)
    {
        $this->addFlashMessage('案例类型修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->casetypeRepository->update($casetype);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetype $casetype
     * @return void
     */
    public function deleteAction(\Jykj\CaseTab\Domain\Model\Casetype $casetype)
    {
        $this->addFlashMessage('案例类型删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->casetypeRepository->remove($casetype);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }
}
