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
 * CasetabController
 */
class CasetabController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * casetabRepository
     * 
     * @var \Jykj\CaseTab\Domain\Repository\CasetabRepository
     * @inject
     */
    protected $casetabRepository = null;
    
    /**
     * casetypeRepository
     *
     * @var \Jykj\CaseTab\Domain\Repository\CasetypeRepository
     * @inject
     */
    protected $casetypeRepository = null;
    
    /**
     * dictitemRepository
     *
     * @var \Jykj\Dicts\Domain\Repository\DictitemRepository
     * @inject
     */
    protected $dictitemRepository = null;

    public function initializeAction (){
        if($this->request->hasArgument('casetab')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('casetab')->getPropertyMappingConfiguration();
            //时间类型修改
            $propertyMappingConfiguration->forProperty('datetime')->setTypeConverterOption('TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter', \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d' );
        }
    }
    
    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $hylist = $this->dictitemRepository->findAlls("",6);//行业分类
        $cplist = $this->dictitemRepository->findAlls("",7);//产品分类
        $bqlist = $this->dictitemRepository->findAlls("",8);//项目标签
        
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $industry = $this->request->hasArgument('industry') ? $this->request->getArgument('industry') : '';
        $product = $this->request->hasArgument('product') ? $this->request->getArgument('product') : '';
        $labels = $this->request->hasArgument('labels') ? $this->request->getArgument('labels') : '';
        
        $casetabs = $this->casetabRepository->findAlls($keyword,$industry,$product,$labels,0,0);
        $this->view->assign('casetabs', $casetabs);
        if ($_GET['tx_casetab_casetab']['@widget_0']['currentPage']) {
            $page = $_GET['tx_casetab_casetab']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $this->view->assign('page', $page);
        
        $this->view->assign('keyword', $keyword);
        $this->view->assign('industry', $industry);
        $this->view->assign('product', $product);
        $this->view->assign('labels', $labels);
        
        $this->view->assign('hylist', $hylist);
        $this->view->assign('cplist', $cplist);
        $this->view->assign('bqlist', $bqlist);
    }

    /**
     * action show
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetab $casetab
     * @return void
     */
    public function showAction(\Jykj\CaseTab\Domain\Model\Casetab $casetab)
    {
        //浏览次数+1
        $casetab->setHits((int)$casetab->getHits()+1);
        $this->casetabRepository->update($casetab);
        $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
        $persistenceManager->persistAll();
        
        $this->view->assign('casetab', $casetab);
        $this->view->assign('allImage', explode(";",$casetab->getImage()));
        
        //查询行业
        $casetabs = $this->casetabRepository->findAlls("",$casetab->getIndustry(),"","",0,0);
        $this->view->assign('casetabs', $casetabs);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        $hylist = $this->dictitemRepository->findAlls("",6);//行业分类
        $cplist = $this->dictitemRepository->findAlls("",7);//产品分类
        $bqlist = $this->dictitemRepository->findAlls("",8);//项目标签
        
        $this->view->assign('hylist', $hylist);
        $this->view->assign('cplist', $cplist);
        $this->view->assign('bqlist', $bqlist);
    }

    /**
     * action create
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetab $casetab
     * @return void
     */
    public function createAction(\Jykj\CaseTab\Domain\Model\Casetab $casetab)
    {
        $this->addFlashMessage('案例内容新增成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $labels = $this->request->hasArgument('labels') ? $this->request->getArgument('labels') : '';
        if($labels!=""){
            $newLabels=",";
            foreach ($labels as $v){
                $bqlist = $this->dictitemRepository->findIsExist(8,$v);
                if($bqlist->count()>0){
                    $newLabels .=$bqlist->getFirst()->getUid().",";
                }else{
                    $dictitem=array("pid"=>119,"name"=>$v,"sort"=>99,"dicttype"=>8,"crdate"=>time(),"tstamp"=>time());
                    //$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dicts_domain_model_dictitem',$dictitem);
                    $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
                    $databaseConnectionForPages = $connectionPool->getConnectionForTable('tx_dicts_domain_model_dictitem');
                    $databaseConnectionForPages->insert('tx_dicts_domain_model_dictitem',$dictitem);
                    $pageUid = (int)$databaseConnectionForPages->lastInsertId('pages');
                    
                    $newLabels .=$pageUid.",";
                }
            }
            $casetab->setLabels($newLabels);
            //$casetab->setLabels(\json_encode($newLabels,JSON_UNESCAPED_UNICODE));
        }
        
        $product = $this->request->hasArgument('product') ? $this->request->getArgument('product') : '';
        if($product!=""){
            $newproduct=",";
            foreach ($product as $v1){
                $newproduct .=$v1.",";
            }
            $casetab->setProduct($newproduct);
        }
        
        //多图
        if ($_FILES['tx_casetab_casetab']['name']['imgpath'][0]!="") {
            $impath = 'uploads/tx_casetab/';
            if(!is_dir(PATH_site.$impath)){
                mkdir(PATH_site.$impath, 0777);
            }
            
            $nameArr = $_FILES['tx_casetab_casetab']['name']['imgpath'];
            $fileArr = $_FILES['tx_casetab_casetab']['tmp_name']['imgpath'];
            $fname="";
            for($i=0;$i<count($nameArr);$i++){
                $filename3 = md5(uniqid($nameArr[$i])).'.'.end(explode('.', $nameArr[$i]));
                if(GeneralUtility::upload_copy_move($fileArr[$i], PATH_site.$impath.$filename3)){
                    if($fname==""){
                        $fname=$filename3;
                    }else{
                        $fname.=";".$filename3;
                    }
                }
            }
            $casetab->setImage($fname);
        }
        $this->casetabRepository->add($casetab);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
        //$this->redirect('list','Casetab','CaseTab',array("casetype"=>$casetab->getCaseType()->getUid()));
    }

    /**
     * action edit
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetab $casetab
     * @ignorevalidation $casetab
     * @return void
     */
    public function editAction(\Jykj\CaseTab\Domain\Model\Casetab $casetab)
    {
        $this->view->assign('casetab', $casetab);
        $hylist = $this->dictitemRepository->findAlls("",6);//行业分类
        $cplist = $this->dictitemRepository->findAlls("",7);//产品分类
        $bqlist = $this->dictitemRepository->findAlls("",8);//项目标签
        
        $this->view->assign('hylist', $hylist);
        $this->view->assign('cplist', $cplist);
        $this->view->assign('bqlist', $bqlist);
    }

    /**
     * action update
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetab $casetab
     * @return void
     */
    public function updateAction(\Jykj\CaseTab\Domain\Model\Casetab $casetab)
    {
        $this->addFlashMessage('案例内容修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $labels = $this->request->hasArgument('labels') ? $this->request->getArgument('labels') : '';
        if($labels!=""){
            $newLabels=",";
            foreach ($labels as $v){
                $bqlist = $this->dictitemRepository->findIsExist(8,$v);
                if($bqlist->count()>0){
                    $newLabels .=$bqlist->getFirst()->getUid().",";
                }else{
                    $dictitem=array("pid"=>119,"name"=>$v,"sort"=>99,"dicttype"=>8,"crdate"=>time(),"tstamp"=>time());
                    
                    $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
                    $databaseConnectionForPages = $connectionPool->getConnectionForTable('tx_dicts_domain_model_dictitem');
                    $databaseConnectionForPages->insert('tx_dicts_domain_model_dictitem',$dictitem);
                    $pageUid = (int)$databaseConnectionForPages->lastInsertId('pages');
                    
                    $newLabels .=$pageUid.",";
                }
            }
            $casetab->setLabels($newLabels);
            //$casetab->setLabels(\json_encode($newLabels,JSON_UNESCAPED_UNICODE));
        }
        
        $product = $this->request->hasArgument('product') ? $this->request->getArgument('product') : '';
        if($product!=""){
            $newproduct=",";
            foreach ($product as $v1){
                $newproduct .=$v1.",";
            }
            $casetab->setProduct($newproduct);
        }
        
        //多图
        if ($_FILES['tx_casetab_casetab']['name']['imgpath'][0]!="") {
            $impath = 'uploads/tx_casetab/';
            if(!is_dir(PATH_site.$impath)){
                mkdir(PATH_site.$impath, 0777);
            }
            
            $nameArr = $_FILES['tx_casetab_casetab']['name']['imgpath'];
            $fileArr = $_FILES['tx_casetab_casetab']['tmp_name']['imgpath'];
            $fname="";
            for($i=0;$i<count($nameArr);$i++){
                $filename3 = md5(uniqid($nameArr[$i])).'.'.end(explode('.', $nameArr[$i]));
                if(GeneralUtility::upload_copy_move($fileArr[$i], PATH_site.$impath.$filename3)){
                    if($fname==""){
                        $fname=$filename3;
                    }else{
                        $fname.=";".$filename3;
                    }
                }
            }
            if($casetab->getImage()!=""){
                $casetab->setImage($casetab->getImage().";".$fname);
            }else{
                $casetab->setImage($fname);
            }
        }
        
        $this->casetabRepository->update($casetab);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
        //$this->redirect('list','Casetab','CaseTab',array("casetype"=>$casetab->getCaseType()->getUid()));
    }

    /**
     * action delete
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetab $casetab
     * @return void
     */
    public function deleteAction(\Jykj\CaseTab\Domain\Model\Casetab $casetab)
    {
        $this->addFlashMessage('案例内容删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->casetabRepository->remove($casetab);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
        //$this->redirect('list','Casetab','CaseTab',array("casetype"=>$casetab->getCaseType()->getUid()));
    }

    /**
     * action sylist
     * 
     * @return void
     */
    public function sylistAction()
    {
        $limit = (int)$this->settings["limit"]==0?4:(int)$this->settings["limit"];
        $hylist = $this->dictitemRepository->findAlls("",6);//查找行业分类
        $casetabs=array();
        if($hylist->count()>0){
            foreach ($hylist as $v){
                $list = $this->casetabRepository->findCaseList($v->getUid(),$limit);
                if($list->count()>0){
                    $arrCase=array();
                    foreach ($list as $lv){
                        $arrCase[]=$lv;
                    }
                    $casetabs[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"contentarr"=>$arrCase);
                }else{
                    $casetabs[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"contentarr"=>array());
                }
            }
        }
        $this->view->assign('casetabs', $casetabs);
    }

    /**
     * action nylist
     * 
     * @return void
     */
    public function nylistAction()
    {
        $hylist = $this->dictitemRepository->findAlls("",6);//行业分类
        $cplist = $this->dictitemRepository->findAlls("",7);//产品分类
        $bqlist = $this->dictitemRepository->findAlls("",8);//项目标签
        
        $industry = $this->request->hasArgument('industry') ? $this->request->getArgument('industry') : '';//行业
        $product = $this->request->hasArgument('product') ? $this->request->getArgument('product') : '';//产品
        $labels = $this->request->hasArgument('labels') ? $this->request->getArgument('labels') : '';//标签
        
        $pagesize=16;
        $nowpage=1;
        $casetabs = $this->casetabRepository->findAlls("",$industry,$product,$labels,$pagesize,$nowpage);
        $this->view->assign('casetabs', $casetabs);
        
        $this->view->assign('industry', $industry);
        $this->view->assign('product', $product);
        $this->view->assign('labels', $labels);
        
        $this->view->assign('hylist', $hylist);
        $this->view->assign('cplist', $cplist);
        $this->view->assign('bqlist', $bqlist);
    }

    /**
     * action nyajax
     * 
     * @return void
     */
    public function nyajaxAction()
    {
        $act = GeneralUtility::_GP('act');
        if($act=="morelist"){
            $industry = GeneralUtility::_GP('industry');
            $product = GeneralUtility::_GP('product');
            $labels = GeneralUtility::_GP('labels');
            $nowpage = GeneralUtility::_GP('nowpage')==""?2:GeneralUtility::_GP('nowpage');
            $pagesize = 8;
            $casetabs = $this->casetabRepository->findAlls("",$industry,$product,$labels,$pagesize,$nowpage);
            if($casetabs->count()>0){
                $list=array();
                foreach($casetabs as $v){
                    $list[]=array(
                        "uid"=>$v->getUid(),
                        "title"=>$v->getTitle(),
                        "image"=>$v->getImage()==""?"":explode(";",$v->getImage())[0],
                        "industryname"=>$v->getIndustry()->getName(),
                        "yycj"=>$v->getSpare4(),
                        "hits"=>$v->getHits(),
                    );
                }
                $dataArr=array("stat"=>1,"data"=>$list);
                die(\json_encode($dataArr,JSON_UNESCAPED_UNICODE));
            }else{
                $dataArr=array("stat"=>0,"msg"=>"没有更多数据可以加载了！");
                die(\json_encode($dataArr,JSON_UNESCAPED_UNICODE));
            }
        }else if($act=="delimage"){
            //删除图片
            $uid = GeneralUtility::_GP('uid');
            $imgname = GeneralUtility::_GP('imgname');
            $casetab = $this->casetabRepository->findByUid($uid);
            if($casetab){
                $nimg = ";".$imgname.";";
                $gimg = ";".$casetab->getImage().";";
                $lastimg = str_replace($nimg,';',$gimg);
                $lastimg=trim($lastimg, ";");
                $casetab->setImage($lastimg);
                $casetab = $this->casetabRepository->update($casetab);
                $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
                $persistenceManager->persistAll();
                
                if($imgname!=""){
                    $impath = PATH_site.'uploads/tx_casetab/'.$imgname;
                    unlink($impath);
                }
                
                $dataArr=array("stat"=>1,"msg"=>"图片删除成功！");
                die(\json_encode($dataArr,JSON_UNESCAPED_UNICODE));
            }
            $dataArr=array("stat"=>0,"msg"=>"获取信息失败，无法删除图片！");
            die(\json_encode($dataArr,JSON_UNESCAPED_UNICODE));
        }
    }
}
