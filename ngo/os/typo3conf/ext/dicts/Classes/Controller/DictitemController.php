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
 * DictitemController
 */
class DictitemController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * dictitemRepository
     *
     * @var \Jykj\Dicts\Domain\Repository\DictitemRepository
     * @inject
     */
    protected $dictitemRepository = null;
    
    /**
     * dicttypeRepository
     *
     * @var \Jykj\Dicts\Domain\Repository\DicttypeRepository
     * @inject
     */
    protected $dicttypeRepository = null;
    
    
    /**
     * action interface
     * 数据字典请求
     * @return void
     */
    public function interfaceAction()
    {
        $act = GeneralUtility::_GP('act');
        if($act == 'dictlist'){
            $dicttype = GeneralUtility::_GP('dicttype');//1表示请求活动标签；2活动类型；3政治面貌；4所在社区；5性别
            if ($dicttype == '') {
                $jsonArr = array('stat' => -2, 'msg' => '获取数据错误！');
                die(\json_encode($jsonArr,JSON_UNESCAPED_UNICODE));
            }
            $dictitems = $this->dictitemRepository->findAlls("", $dicttype);
            if(count($dictitems)>0){
                $dm=array();
                foreach ($dictitems as $dic){
                    $dm[]=array("uid"=>$dic->getUid(),"name"=>$dic->getName());
                }
                $arrdata=array("stat"=>0,"data"=>$dm);
                $this->printJson($arrdata);
            }else{
                $arrdata=array("stat"=>-3,"msg"=>"没有查询到数据！");
                $this->printJson($arrdata);
            }
        }else{
            $jsonArr = array('stat' => -1, 'msg' => '数据接口错误！');
            $this->printJson($jsonArr);
        }
    }
    
    /**
     * 输出json
     * @param unknown $jsonArr
     */
    public function printJson($jsonArr){
        die(\json_encode($jsonArr,JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $dicttype = $this->request->hasArgument('dicttype') ? $this->request->getArgument('dicttype') : GeneralUtility::_GP('dicttype');
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        if ($_GET['tx_dicts_dictitem']['@widget_0']['currentPage']) {
            $page = $_GET['tx_dicts_dictitem']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $dictitems = $this->dictitemRepository->findAlls($keyword, $dicttype);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('dictitems', $dictitems);
        $this->view->assign('dicttype', $dicttype);
        $this->view->assign('page', $page);
    }
    
    /**
     * action show
     *
     * @param \Jykj\Dicts\Domain\Model\Dictitem $dictitem
     * @return void
     */
    public function showAction(\Jykj\Dicts\Domain\Model\Dictitem $dictitem)
    {
        $this->view->assign('dictitem', $dictitem);
    }
    
    /**
     * action new
     *
     * @return void
     */
    public function newAction()
    {
        $typeuid = $this->request->hasArgument('dicttype') ? $this->request->getArgument('dicttype') : 0;
        $this->view->assign('typeuid', $typeuid);
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
        $sortArr = $data["sort"];//排序
        $typeuid = $data["typeuid"];//类别uid 新增字典项目使用
        
        if($typeuid!=""){
            $objDicttype = $this->dicttypeRepository->findByUid($typeuid);
        }
        
        for($i=0;$i<count($nameArr);$i++){
            $dictitme = new \Jykj\Dicts\Domain\Model\Dictitem;
            $dictitme->setName($nameArr[$i]);
            $dictitme->setSort($sortArr[$i]);
            $dictitme->setDicttype($objDicttype);
            $this->dictitemRepository->add($dictitme);
            $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
            $persistenceManager->persistAll();
        }
        
        $this->addFlashMessage('字典项目新增成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list', 'Dictitem', 'Dicts', ['dicttype' => $typeuid!=""?$typeuid:$objParent->getDicttype()->getUid()]);
        
    }
    
    /**
     * action edit
     *
     * @param \Jykj\Dicts\Domain\Model\Dictitem $dictitem
     * @ignorevalidation $dictitem
     * @return void
     */
    public function editAction(\Jykj\Dicts\Domain\Model\Dictitem $dictitem)
    {
        $uid = $this->request->hasArgument('dictitem') ? $this->request->getArgument('dictitem') : 0;
        $dictitem = $this->dictitemRepository->findByuid($uid);
        $this->view->assign('dictitem', $dictitem);
        $this->view->assign('typeuid', $dictitem->getDicttype());
    }
    
    /**
     * action update
     *
     * @param \Jykj\Dicts\Domain\Model\Dictitem $dictitem
     * @return void
     */
    public function updateAction(\Jykj\Dicts\Domain\Model\Dictitem $dictitem)
    {
        $this->addFlashMessage('字典项目修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->dictitemRepository->update($dictitem);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list', 'Dictitem', 'Dicts', ['dicttype' => $dictitem->getDicttype()->getUid()]);
    }
    
    /**
     * action delete
     *
     * @param \Jykj\Dicts\Domain\Model\Dictitem $dictitem
     * @return void
     */
    public function deleteAction(\Jykj\Dicts\Domain\Model\Dictitem $dictitem)
    {
        $uid = $this->request->hasArgument('dictitem') ? $this->request->getArgument('dictitem') : 0;
        $dictitem = $this->dictitemRepository->findByuid($uid);
        
        $this->addFlashMessage('字典项目删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->dictitemRepository->remove($dictitem);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list', 'Dictitem', 'Dicts', ['dicttype' => $dictitem->getDicttype()->getUid()]);
    }
    
}