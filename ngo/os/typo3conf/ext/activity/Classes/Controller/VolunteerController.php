<?php
namespace Jykj\Activity\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
/***
 *
 * This file is part of the "志愿者活动" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code. 
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/

/**
 * VolunteerController
 */
class VolunteerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * volunteerRepository
     * 
     * @var \Jykj\Activity\Domain\Repository\VolunteerRepository
     * @inject
     */
    protected $volunteerRepository = null;
    
    /**
     * activityRepository
     * 
     * @var \Jykj\Activity\Domain\Repository\ActivityRepository
     * @inject
     */
    protected $activityRepository = null;

    /**
     * signupRepository
     * 
     * @var \Jykj\Activity\Domain\Repository\SignupRepository
     * @inject
     */
    protected $signupRepository1 = null;
    
    /**
     * dictitemRepository
     *
     * @var \Jykj\Dicts\Domain\Repository\DictitemRepository
     * @inject
     */
    protected $dictitemRepository = null;

    /**
     * areaRepository
     * 
     * @var \Jykj\Dicts\Domain\Repository\AreaRepository
     * @inject
     */
    protected $areaRepository = null;

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        if ($_GET['tx_activity_volunteer']['@widget_0']['currentPage']) {
            $page = $_GET['tx_activity_volunteer']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $province = $this->request->hasArgument('province') ? $this->request->getArgument('province') : '';
        $volunteers = $this->volunteerRepository->findAll($keyword,$province);
        $this->view->assign('volunteers', $volunteers);
        $this->view->assign('arrArea', $this->areaRepository->findAllArea(1));
        $this->view->assign('arrSex', $this->dictitemRepository->findAlls("", 5));
        $this->view->assign('arrZzmm', $this->dictitemRepository->findAlls("", 3));
        $this->view->assign('page', $page);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('province', $province);
        
        //excel export
        if($this->request->hasArgument('excelExport')){             
            $phpExcelService = GeneralUtility::makeInstanceService('phpexcel');
            $phpExcel = $phpExcelService->getPHPExcel();
            $sheet  = $phpExcel->setActiveSheetIndex(0);
            $dataArray[] = array('姓名', '性别','出生日期','政治面貌','邮箱','电话','QQ','微信','新浪微博','个人简介','有无愿者经验','技能专长');
            if($volunteers->count()){
                foreach($volunteers as $volunteer){
                    $dataArray[] = array(
                        $volunteer->getName(),
                        $volunteer->getSex()->getName(),
                        $volunteer->getBirthday(),
                        $volunteer->getIdentity()->getName(),
                        $volunteer->getEmail(),
                        ''.$volunteer->getTelephone(),
                        $volunteer->getQqcode(),
                        $volunteer->getWechat(),
                        $volunteer->getWeibo(),
                        $volunteer->getDescritpion(),
                        $volunteer->getIsexperience()==1?"有":"无",
                        $volunteer->getSkill()
                    );
                }
                $sheet->fromArray($dataArray, NULL, 'A1');
                $objWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel2007', $phpExcel);
                $fileName = '志愿者数据_'.date('Y-m-d');
                header('Pragma: public');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Type: application/force-download');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit;
            }
        }
    }

    /**
     * action show
     * 
     * @param \Jykj\Activity\Domain\Model\Volunteer $volunteer
     * @return void
     */
    public function showAction(\Jykj\Activity\Domain\Model\Volunteer $volunteer)
    {
        $this->view->assign('volunteer', $volunteer);
        $this->view->assign('arrArea', $this->areaRepository->findAllArea(1));
        $this->view->assign('arrSex', $this->dictitemRepository->findAlls("", 5));
        $this->view->assign('arrZzmm', $this->dictitemRepository->findAlls("", 3));
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        //$this->view->assign('arrArea', $this->areaRepository->findAllArea(1));
        $this->view->assign('arrSex', $this->dictitemRepository->findAlls("", 5));
        $this->view->assign('arrComm', $this->dictitemRepository->findAlls("", 4));
        $this->view->assign('arrZzmm', $this->dictitemRepository->findAlls("", 3));
        if (GeneralUtility::_GP('name')) {
            $volunteer = new \Jykj\Activity\Domain\Model\Volunteer();
            $volunteer->setName(GeneralUtility::_GP('name'));
            $volunteer->setTelephone(GeneralUtility::_GP('telephone'));
            $this->view->assign('from', GeneralUtility::_GP('from'));
            $this->view->assign('actuid', GeneralUtility::_GP('actid'));
            $this->view->assign('volunteer', $volunteer);
        }
    }

    /**
     * action create
     * 
     * @param \Jykj\Activity\Domain\Model\Volunteer $volunteer
     * @return void
     */
    public function createAction(\Jykj\Activity\Domain\Model\Volunteer $volunteer)
    {
        $this->volunteerRepository->add($volunteer);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $persistenceManager = $this->objectManager->get(PersistenceManager::class);
        $persistenceManager->persistAll();
        
        $from = $this->request->hasArgument('from') ? $this->request->getArgument('from') : 'add';
        $actuid = $this->request->hasArgument('actuid') ? $this->request->getArgument('actuid') : '';
        if ($from!='' && $actuid!='') {
            $activity = $this->activityRepository->findByUid($actuid);
            $signup = new \Jykj\Activity\Domain\Model\Signup();
            $signup->setActivityuid($activity);
            $signup->setVolunteer($volunteer);
            if ($from=='bm') {
                $signup->setSigntime(time());
                $signup->setStatus(0);
                $message = '报名成功!';
            }
            if ($from=='qd') {
                $signup->setChecktime(time());
                $signup->setStatus(2);
                $message = '签到成功!';
            }
            
            $this->signupRepository1->add($signup);
            $persistenceManager = $this->objectManager->get(PersistenceManager::class);
            $persistenceManager->persistAll();
        }else{
            $message = '加入志愿者成功!';
        }
        $this->redirect('success', 'Volunteer', 'Activity', array('from'=>$from,'message'=>$message));
    }

    /**
     * action edit
     * 
     * @param \Jykj\Activity\Domain\Model\Volunteer $volunteer
     * @ignorevalidation $volunteer
     * @return void
     */
    public function editAction(\Jykj\Activity\Domain\Model\Volunteer $volunteer)
    {
        $this->view->assign('volunteer', $volunteer);
        //$this->view->assign('arrArea', $this->areaRepository->findAllArea(1));
        $this->view->assign('arrSex', $this->dictitemRepository->findAlls("", 5));
        $this->view->assign('arrZzmm', $this->dictitemRepository->findAlls("", 3));
        $this->view->assign('arrComm', $this->dictitemRepository->findAlls("", 4));
        if (GeneralUtility::_GP('name')) {
            $volunteer = new \Jykj\Activity\Domain\Model\Volunteer();
            $volunteer->setName(GeneralUtility::_GP('name'));
            $volunteer->setTelephone(GeneralUtility::_GP('telephone'));
            $this->view->assign('from', GeneralUtility::_GP('from'));
            $this->view->assign('actuid', GeneralUtility::_GP('actid'));
            $this->view->assign('volunteer', $volunteer);
        }
    }

    /**
     * action update
     * 
     * @param \Jykj\Activity\Domain\Model\Volunteer $volunteer
     * @return void
     */
    public function updateAction(\Jykj\Activity\Domain\Model\Volunteer $volunteer)
    {
        $this->addFlashMessage('修改成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->volunteerRepository->update($volunteer);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Activity\Domain\Model\Volunteer $volunteer
     * @return void
     */
    public function deleteAction(\Jykj\Activity\Domain\Model\Volunteer $volunteer)
    {
        $this->addFlashMessage('删除成功', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->volunteerRepository->remove($volunteer);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action success
     * 
     * @return void
     */
    public function ajaxAction()
    {
        $act = GeneralUtility::_GP('act');
        $uid = GeneralUtility::_GP('uid'); //新增uid=0,修改uid为对应的记录值
        $telephone = GeneralUtility::_GP('telephone');
        $email = GeneralUtility::_GP('email');
        if($act == 'add'){
            //新增，校验电话和邮箱
            $iRet=$this->volunteerRepository->checkData($uid,$telephone,$email);
            if($iRet==0){
                //不存在，校验通过
                print "true";
            }else{
                print "false";
            }
            exit();
        }else if($act == 'edit'){
            //修改，校验电话和邮箱
            $iRet=$this->volunteerRepository->checkData($uid,$telephone,$email);
            if($iRet==0){
                print "true";
            }else{
                print "false";
            }
            exit();
        }else{
            $jsonArr = array('stat' => 0, 'msg' => '数据请求错误！');
            die(\json_encode($jsonArr,JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * action success
     * 
     * @return void
     */
    public function successAction()
    {
        $from = $this->request->hasArgument('from') ? $this->request->getArgument('from') : GeneralUtility::_GP('name');
        $message = $this->request->hasArgument('message') ? $this->request->getArgument('message') : GeneralUtility::_GP('message');
        $this->view->assign('from', $from);
        $this->view->assign('message', $message);
        
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
            
            $iRet=$this->volunteerRepository->deleteByUidstring($item);
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
        $this->addFlashMessage('没有可删除的对象！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirect('list');
    }
}
