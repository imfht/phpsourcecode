<?php
namespace Jykj\Activity\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

use TYPO3\CMS\Core\Database\ConnectionPool;
/***
 *
 * This file is part of the "志愿者活动" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information,  please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * SignupController
 */
class SignupController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

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
    protected $signupRepository = null;

    /**
     * volunteerRepository
     * 
     * @var \Jykj\Activity\Domain\Repository\VolunteerRepository
     * @inject
     */
    protected $volunteerRepository = null;
    
    /**
     * dictitemRepository
     *
     * @var \Jykj\Dicts\Domain\Repository\DictitemRepository
     * @inject
     */
    protected $dictitemRepository = null;

    //年龄阶段
    protected $ageType = [
        ["uid" => 0, "name" => "2010年-至今"],
        ["uid" => 1, "name" => "1980年-2009年"],
        ["uid" => 2, "name" => "1960年-1979年"],
        ["uid" => 3, "name" => "1940年-1959年"],
        ["uid" => 4, "name" => "1939年及以前"]
    ];

    //统计类型
    protected $countTypes = [
        ["uid" => 0, "name" => "志愿者活动参与统计"],
        ["uid" => 1, "name" => "志愿者参与类别及人数统计"],
        ["uid" => 2, "name" => "志愿者年龄分布统计"],
        ["uid" => 3, "name" => "志愿者所在地分布统计"],
        ["uid" => 4, "name" => "活动分类统计"]
    ];

    //周
    protected $weeks = [
        ["uid" => 1, "name" => "每周一"],
        ["uid" => 2, "name" => "每周二"],
        ["uid" => 3, "name" => "每周三"],
        ["uid" => 4, "name" => "每周四"],
        ["uid" => 5, "name" => "每周五"],
        ["uid" => 6, "name" => "每周六"],
        ["uid" => 7, "name" => "每周日"]
    ];

    /**
     * action interface
     * 接口调用
     * 
     * @return void
     */
    public function interfaceAction()
    {
        $act = GeneralUtility::_GP('act');
        if ($act == 'myactivity') {

            //我的活动列表
            $useruid = GeneralUtility::_GP('useruid');
            $pagesize = GeneralUtility::_GP('pagesize');

            //每页记录数
            $nowpage = GeneralUtility::_GP('nowpage');

            //当前页
            $stdate = GeneralUtility::_GP('stdate');

            //开始时间
            $eddate = GeneralUtility::_GP('eddate');

            //结束时间
            if (useruid == '' || $pagesize == "" || $nowpage == "") {
                $jsonArr = ['stat' => -2, 'msg' => '获取数据错误！'];
                $this->printJson($jsonArr);
            }
            $signups = $this->signupRepository->findMySignActivity($useruid, $pagesize, $nowpage, $stdate, $eddate);
            if (count($signups) > 0) {
                $arrData = [];

                //志愿者姓名
                $userData = ["name" => $signups->getFirst()->getUseruid()->getName()];
                foreach ($signups as $v) {
                    $arrData[] = [
                        "uid" => $v->getActivityuid()->getUid(),
                        "title" => $v->getActivityuid()->getName(),
                        "date" => $v->getActivityuid()->getWay() == 0 ? $v->getActivityuid()->getSttime()->format('Y-m-d H:i') : $v->getSigntime()->format('Y-m-d ') . $v->getActivityuid()->getHour(),
                        "imgpath" => $v->getActivityuid()->getPictures() == "" ? GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'uploads/tx_activity/imagesdefault.png' : GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'uploads/tx_activity/images' . $v->getActivityuid()->getPictures()
                    ];
                }
                $jsonArr = ['stat' => 0, 'data' => $arrData, 'user' => $userData];
                $this->printJson($jsonArr);
            } else {
                if ($stdate != "" || $eddate != "") {
                    $jsonArr = ['stat' => -4, 'msg' => '没有查询到您参加的活动！'];
                } else {
                    if ($nowpage == 1) {
                        $jsonArr = ['stat' => -3, 'msg' => '亲您还没有参加活动！'];
                    } else {
                        $jsonArr = ['stat' => 1, 'msg' => '已经显示您参加的全部活动！'];
                    }
                }
                $this->printJson($jsonArr);
            }
        } else {
            if ($act == 'tagcount') {

                //我的参与活动统计【按照活动标签】
                $useruid = GeneralUtility::_GP('useruid');
                if ($useruid == '') {
                    $jsonArr = ['stat' => -2, 'msg' => '获取数据错误！'];
                    $this->printJson($jsonArr);
                }

                //统计，暂时按照单独标签的形式进行计算
                $couSql = "select uid,name,case when num is null then 0 else num end num\n    \t\t\t\tfrom tx_dicts_domain_model_dictitem c left join \n    \t\t\t\t(SELECT b.tag,count(1) num FROM tx_activity_domain_model_signup a,\n    \t\t\t\ttx_activity_domain_model_activity b where a.activityuid=b.uid and a.deleted=0 \n    \t\t\t\tand a.hidden=0 and b.deleted=0 and b.hidden=0 and useruid=" . $useruid . " group by b.tag) d \n    \t\t\t\ton c.uid=d.tag where deleted=0 and dicttype=2 order by sort asc";
                $signups = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("*", "(" . $couSql . ") tab", "", "", "");
                $arrName = [];
                $arrData = [];
                foreach ($signups as $v) {
                    $arrName[] = $v["name"];
                    $arrData[] = $v["num"];
                }
                $jsonArr = ['stat' => 0, 'name' => $arrName, 'data' => $arrData];
                $this->printJson($jsonArr);
            } else {
                if ($act == 'signlist') {

                    //正在发布的活动
                    $pagesize = GeneralUtility::_GP('pagesize');

                    //每页记录数
                    $nowpage = GeneralUtility::_GP('nowpage');

                    //当前页
                    if (useruid == '') {
                        $jsonArr = ['stat' => -2, 'msg' => '获取数据错误！'];
                        $this->printJson($jsonArr);
                    }
                    $signups = $this->activityRepository->findSignNowActivities($pagesize, $nowpage);
                    if (count($signups) > 0) {
                        $arrData = [];
                        foreach ($signups as $v) {
                            $arrData[] = [
                                "uid" => $v->getUid(),
                                "title" => $v->getName(),
                                "date" => $v->getWay() == 0 ? $v->getSttime()->format('Y-m-d H:i') : $this->weeks[$v->getWeek() - 1]["name"] . $v->getHour()
                            ];
                        }
                        $jsonArr = ['stat' => 0, 'data' => $arrData];
                        $this->printJson($jsonArr);
                    } else {
                        if ($nowpage == 1) {
                            $jsonArr = ['stat' => -3, 'msg' => '亲管理员还没有发布活动或者活动已经过期！'];
                        } else {
                            $jsonArr = ['stat' => 1, 'msg' => '已经显示您参加的全部活动！'];
                        }
                        $this->printJson($jsonArr);
                    }
                } else {
                    $jsonArr = ['stat' => -1, 'msg' => '数据接口错误！'];
                    $this->printJson($jsonArr);
                }
            }
        }
    }

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $activity = $this->request->hasArgument('activity') ? $this->request->getArgument('activity') : GeneralUtility::_GP('activity');
        $tab = $this->request->hasArgument('tab') ? $this->request->getArgument('tab') : GeneralUtility::_GP('tab');
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        if ($_GET['tx_activity_signup']['@widget_0']['currentPage']) {
            $page = $_GET['tx_activity_signup']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $signups = $this->signupRepository->findAlls($activity, $tab, $keyword);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('signups', $signups);
        $this->view->assign('page', $page);
        $this->view->assign('tab', $tab);
        $this->view->assign('activity', $activity);

        //excel export
        if ($this->request->hasArgument('excelExport')) {
            $objactivity = $this->activityRepository->findByUid($activity);
            $phpExcelService = GeneralUtility::makeInstanceService('phpexcel');
            $phpExcel = $phpExcelService->getPHPExcel();
            $sheet = $phpExcel->setActiveSheetIndex(0);
            // $extitle = ($tab=='signin') ? '报名' : '签到';
            $dataArray[] = ['序号', '姓名', '性别', '联系方式', '邮箱','出生年月', '省份', '政治面貌', '报名时间','签到时间'];
            if ($signups->count() > 0) {
                $i = 1;
                foreach ($signups as $signup) {
                    // $extime = ($tab=='signin') ? $signup->getSigntime() : $signup->getChecktime();
                    $dataArray[] = [
                        $i++,
                        $signup->getVolunteer()->getName(),
                        $signup->getVolunteer()->getSex()->getName(),
                        " " . $signup->getVolunteer()->getTelephone(),
                        $signup->getVolunteer()->getEmail(),
                        $signup->getVolunteer()->getBirthday(),
                        ($signup->getVolunteer()->getProvince()) ? $signup->getVolunteer()->getProvince()->getName() : '无',
                        $signup->getVolunteer()->getIdentity()->getName(), 
                        date('Y-m-d H:i:s',$signup->getSigntime()==null ? ' ':$signup->getSigntime()),
                        date('Y-m-d H:i:s',$signup->getChecktime())
                    ];
                }
                $sheet->fromArray($dataArray, NULL, 'A1');
                $objWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel2007', $phpExcel);
                $fileName = $objactivity->getName() . $extitle.'表_' . date('Y-m-d');
                header('Pragma: public');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Type: application/force-download');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit;
            }
        }
    }

    /**
     * action show
     * 
     * @param \Jykj\Activity\Domain\Model\Signup $signup
     * @return void
     */
    public function showAction(\Jykj\Activity\Domain\Model\Signup $signup)
    {
        $this->view->assign('signup', $signup);
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
     * action edit
     * 
     * @param \Jykj\Activity\Domain\Model\Signup $signup
     * @ignorevalidation $signup
     * @return void
     */
    public function editAction(\Jykj\Activity\Domain\Model\Signup $signup)
    {
        $this->view->assign('signup', $signup);
    }
    
    /**
     * action signup
     * 
     * @return void
     */
    public function signupAction()
    {
        $actuid = GeneralUtility::_GP('activity');
        $activity = $this->activityRepository->findByUid($actuid);
        // var_dump($activity);
        if ($activity->getSendstat()==2) {
            $this->redirect('success', 'Signup', 'Activity', array('from'=>'','flag'=>'1','message'=>'该活动已下线!'));
        }else{
            $this->view->assign('flag', GeneralUtility::_GP('flag'));
            $this->view->assign('activity', $activity);
        }
    }

    /**
     * action create
     * 
     * @param \Jykj\Activity\Domain\Model\Signup $signup
     * @return void
     */
    public function createAction(\Jykj\Activity\Domain\Model\Signup $signup)
    {
        $this->addFlashMessage('保存成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->signupRepository->add($signup);
        $this->redirect('list');
    }

    /**
     * action update
     * 
     * @return void
     */
    public function updateAction()
    {
        $flag = $this->request->hasArgument('flag') ? $this->request->getArgument('flag') : '';
        $actid = $this->request->hasArgument('actid') ? $this->request->getArgument('actid') : '';
        $name = $this->request->hasArgument('name') ? $this->request->getArgument('name') : '';
        $telephone = $this->request->hasArgument('telephone') ? $this->request->getArgument('telephone') : '';
        $activity = $this->activityRepository->findByUid($actid);
        // 报名
        if ($flag=='signin') {
            $signupRes = $this->signupRepository->findIsSign($telephone,$actid,0);
            if ($signupRes->count()>0) {
                $this->redirect('success', 'Signup', 'Activity', array('from'=>'bm','flag'=>'1','message'=>'您已报名!'));
            } else {
                $volun = $this->volunteerRepository->findByTelephone($telephone);
                if ($volun->count()==0) {
                    //没有登记,跳转去登记
                    $uri = $this->uriBuilder->reset()->setTargetPageUid($this->settings['volpage'])->setArguments(['from' => 'bm','telephone' => $telephone,'name'=>$name,'actid'=>$actid])->uriFor('new', array(), 'Volunteer', 'Activity', 'activity');
                    header("location:$uri");
                }else{
                    $signup = new \Jykj\Activity\Domain\Model\Signup();
                    // $signup->setName($name);
                    // $signup->setTelephone($telephone);
                    $signup->setStatus(0);
                    $signup->setSigntime(time());
                    $signup->setActivityuid($activity);
                    $signup->setVolunteer($volun->getFirst());
                    $this->signupRepository->add($signup);
                    $persistenceManager = $this->objectManager->get(PersistenceManager::class);
                    $persistenceManager->persistAll();
                    $this->redirect('success', 'Signup', 'Activity', array('from'=>'bm','flag'=>'0','message'=>'报名成功!'));
                }
                
            }
        }
        //签到
        if ($flag=='checkin') {
            //查找签到的
            $checkRes = $this->signupRepository->findIsSign($telephone,$actid,1);
            if ($checkRes->count()>0) {
                $this->redirect('success', 'Signup', 'Activity', array('from'=>'qd','flag'=>'1','message'=>'您已签到!'));
            } else {
                //查找报名的
                $signupRes = $this->signupRepository->findIsSign($telephone,$actid,0);
                if ($signupRes->count()>0) {
                    $signup = $signupRes->getFirst();
                    $signup->setStatus(2);
                    $signup->setChecktime(time());
                    $this->signupRepository->update($signup);
                    $persistenceManager = $this->objectManager->get(PersistenceManager::class);
                    $persistenceManager->persistAll();
                    $this->redirect('success', 'Signup', 'Activity', array('from'=>'qd','flag'=>'0','message'=>'签到成功!'));
                } else {
                    $volun = $this->volunteerRepository->findByTelephone($telephone);
                    if ($volun->count()==0) {
                        //没有登记,跳转去登记
                        $uri = $this->uriBuilder->reset()->setTargetPageUid($this->settings['volpage'])->setArguments(['from' => 'qd','telephone' => $telephone,'name'=>$name,'actid'=>$actid])->uriFor('new', array(), 'Volunteer', 'Activity', 'activity');
                        header("location:$uri");
                    }else{
                        $signup = new \Jykj\Activity\Domain\Model\Signup();
                        // $signup->setName($name);
                        // $signup->setTelephone($telephone);
                        $signup->setStatus(2);
                        $signup->setChecktime(time());
                        $signup->setSigntime(time()); 
                        $signup->setActivityuid($activity);
                        $signup->setVolunteer($volun->getFirst());
                        $this->signupRepository->add($signup);
                        $persistenceManager = $this->objectManager->get(PersistenceManager::class);
                        $persistenceManager->persistAll();
                        $this->redirect('success', 'Signup', 'Activity', array('from'=>'qd','flag'=>'0','message'=>'签到成功!'));
                    }
                }
            }
        }
    }

    /**
     * action delete
     * 
     * @param \Jykj\Activity\Domain\Model\Signup $signup
     * @return void
     */
    public function deleteAction(\Jykj\Activity\Domain\Model\Signup $signup)
    {
        $actid = $signup->getActivityuid()->getUid();
        $this->addFlashMessage('删除成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->signupRepository->remove($signup);
        $this->redirect('list', 'Signup', 'Activity', array('activity'=>$actid));
    }

    /**
     * action iinterface
     * 
     * @return void
     */
    public function iinterfaceAction()
    {
    }


    /**
     * 数据统计
     *
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function statisticsAction(){
    	$ctypes = $this->request->hasArgument('ctypes') ? $this->request->getArgument('ctypes') : 0;
    	$sttime = $this->request->hasArgument('sttime') ? $this->request->getArgument('sttime') : '';
    	$overtime = $this->request->hasArgument('overtime') ? $this->request->getArgument('overtime') : '';
    	$strWhere="";
    	if($ctypes==0){
    	    $records=$this->signupRepository->zyzHdTj($sttime,$overtime);
	        $dictTypes = $this->dictitemRepository->findAlls("", 2);
	        if($dictTypes->count()>0){
	            $inum=0;
	            foreach($dictTypes as $v){
    	            $flag=0;
	                for($i=0;$i<count($records);$i++){
	                    if($records[$i]["tag"]==$v->getUid()){
	                        $inum +=$records[$i]["num"];
	                        $tagList[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"num"=>$records[$i]["num"]);
	                        $flag=1;
    	                    break;
	                    }
	                }
	                if($flag==0){
	                    $tagList[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"num"=>0);
	                }
	            }
	            $tagList[]=array("uid"=>0,"name"=>"总计","num"=>$inum);
	        }else{
	            $tagList[]=array("uid"=>0,"name"=>"总计","num"=>0);
	        }
    	}else if($ctypes==1){
    		//志愿者参与类别及人数
    	    $records=$this->signupRepository->zyzCylbAndRs($sttime,$overtime);
    	    $tallnum=0;
    	    $pallnum=0;
	        for($j=0;$j<count($records);$j++){
	            $tallnum +=$records[$j]["tnum"];
	            $pallnum +=$records[$j]["pnum"];
	        }
    	        
    	    $dictTypes = $this->dictitemRepository->findAlls("", 2);
    	    if($dictTypes->count()>0){
    	        foreach($dictTypes as $v){
    	            $flag=0;
    	            $tper="0.00%";
    	            $pper="0.00%";
    	            for($i=0;$i<count($records);$i++){
    	                if($records[$i]["tag"]==$v->getUid()){
    	                    $tper=(round($records[$i]["tnum"]/$tallnum,4)*100)."%";
    	                    $pper=(round($records[$i]["pnum"]/$pallnum,4)*100)."%";
    	                    
    	                    $tagList[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"tnum"=>$records[$i]["tnum"],"tpercent"=>$tper,"pnum"=>$records[$i]["pnum"],"ppercent"=>$pper);
    	                    $flag=1;
    	                    break;
    	                }
    	            }
    	            if($flag==0){
    	                $tagList[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"tnum"=>0,"tpercent"=>"0.00%","pnum"=>0,"ppercent"=>"0.00%");
    	            }
    	        }
    	        $tper=$tallnum==0?'0.00%':(round($tallnum/$tallnum,4)*100)."%";
    	        $pper=$pallnum==0?'0.00%':(round($pallnum/$pallnum,4)*100)."%";
    	        $tagList[]=array("uid"=>0,"name"=>"总计","tnum"=>$tallnum,"tpercent"=>$tper,"pnum"=>$pallnum,"ppercent"=>$pper);
    	    }else{
    	        $tagList[]=array("uid"=>0,"name"=>"总计","tnum"=>0,"tpercent"=>"0.00%","pnum"=>0,"ppercent"=>"0.00%");
    	    }
    	}else if($ctypes==2){
    		// //按照年龄统计
    	    $records=$this->signupRepository->zyzNlTj($sttime,$overtime);
    		$tallnum=0;
    		$pallnum=0;
    		foreach($records as $v){
    		    $tallnum +=$v->getPid();
    		    $pallnum +=$v->getUid();
    		}
    		
    		$tagList=array();
    		foreach($this->ageType as $v){
    			$tnum=0;//次数
    			$tper=0;//次数百分比
    			$pnum=0;//人数
    			$pper=0;//人数百分比
    			foreach($records as $res){
    			    if($v["uid"]==$res->getStatus()){
    			        $tnum=$res->getPid();
    			        $pnum=$res->getUid();
    					break;
    				}
    			}
    			$tper=$tallnum==0?"0.00%":(round($tnum/$tallnum,4)*100)."%";
    			$pper=$pallnum==0?"0.00%":(round($pnum/$pallnum,4)*100)."%";
    			$tagList[]=array("uid"=>$v["uid"],"name"=>$v["name"],"tnum"=>$tnum,"tpercent"=>$tper,"pnum"=>$pnum,"ppercent"=>$pper);
    		}
    		$tper=$tallnum==0?"0.00%":"100.00%";
    		$pper=$pallnum==0?"0.00%":"100.00%";
    		$tagList[]=array("uid"=>0,"name"=>"总计","tnum"=>$tallnum,"tpercent"=>$tper,"pnum"=>$pallnum,"ppercent"=>$pper);
    	}else if($ctypes==3){
    		//按照社区统计
    	    $list=$this->signupRepository->sqTj($sttime,$overtime);
    	    $tallnum=0;
    	    for($j=0;$j<count($list);$j++){
    	        $tallnum +=$list[$j]["tnum"];
    	    }
    	    
    	    $dictTypes = $this->dictitemRepository->findAlls("", 4);
    	    if($dictTypes->count()>0){
    	        foreach($dictTypes as $v){
    	            $flag=0;
        	        for($i=0;$i<count($list);$i++){
        	            if($list[$i]["community"]==$v->getUid()){
        	        		$tper="0.00%";
        	        		$tper=(round($list[$i]["tnum"]/$tallnum,4)*100)."%";
        	        		$tagList[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"tnum"=>$list[$i]["tnum"],"tpercent"=>$tper);
        	        		$tnum+=$list[$i]["tnum"];
        	        		$flag=1;
        	        		break;
        	            }
    	        	}
    	        	
    	        	if($flag==0){
    	        	    $tagList[]=array("uid"=>$v->getUid(),"name"=>$v->getName(),"tnum"=>0,"tpercent"=>"0.00%");
    	        	}
    	        }
	        	$tperall=$tallnum==0?"0.00%":(round($tnum/$tallnum,4)*100)."%";
	        	$tagList[]=array("uid"=>0,"name"=>"总计","tnum"=>$tnum,"tpercent"=>$tperall);
    	    }else{
    	        $tagList[]=array("uid"=>0,"name"=>"总计","tnum"=>0,"tpercent"=>"0.00%");
    	    }
    	}else if($ctypes==4){
    		//按照类型统计
    		$list=$this->signupRepository->lxTj($sttime,$overtime);
    		//大类
    		$arrBigItem = $this->dictitemRepository->findAlls("", 1);
    		//小类
    		$arrSmallItem = $this->dictitemRepository->findAlls("", 2);
    		$tagList=array();
    		$alltnum=0;//总计次数
    		$allpnum=0;//总计人数
    		foreach($arrBigItem as $bv){
    			$arrSmdata=array();
    			$bigtnum=0;//每个大类次数
    			$bigpnum=0;//每个大类人数
    			foreach($arrSmallItem as $sv){
    				$tnum=0;//每个小类次数
    				$pnum=0;//每个小类人数
    				for($i=0;$i<count($list);$i++){
    					if($bv->getUid()==$list[$i]["types"] && $sv->getUid()==$list[$i]["tag"]){
    						$tnum +=$list[$i]["tnum"];
    						$pnum +=$list[$i]["pnum"];
    						$bigtnum +=$list[$i]["tnum"];
    						$bigpnum +=$list[$i]["pnum"];
    					}
    				}
    				$arrSmdata[]=array("suid"=>$sv->getUid(),"sname"=>$sv->getName(),"tnum"=>$tnum,"pnum"=>$pnum);
    			}
    			$alltnum +=$bigtnum;
    			$allpnum +=$bigpnum;
    			$arrSmdata[]=array("suid"=>0,"sname"=>"总数","tnum"=>$bigtnum,"pnum"=>$bigpnum);
    			$tagList[]=array("buid"=>$bv->getUid(),"bname"=>$bv->getName(),"data"=>$arrSmdata);
    		}
    		$tagList[]=array("buid"=>0,"bname"=>"总计","data"=>array("suid"=>0,"sname"=>"","tnum"=>$alltnum,"pnum"=>$allpnum));
    	}
    	//SELECT from_unixtime(birthday,'%Y-%m-%d'),TIMESTAMPDIFF(YEAR, from_unixtime(birthday,'%Y-%m-%d'), CURDATE()) age from fe_users
    	$this->view->assign('tagList', $tagList);
    	$this->view->assign('ctypes', $ctypes);
    	$this->view->assign('countTypes', $this->countTypes);
    	$this->view->assign('sttime', $sttime);
    	$this->view->assign('overtime', $overtime);
    }

    /**
     * action mylist
     * 我报名参加的活动列表
     * 
     * @return void
     */
    public function mylistAction()
    {
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        $useruid = $this->request->hasArgument('useruid') ? $this->request->getArgument('useruid') : GeneralUtility::_GP('useruid');
        if ($_GET['tx_activity_activity']['@widget_0']['currentPage']) {
            $page = $_GET['tx_activity_activity']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $signups = $this->signupRepository->findMyActivity($useruid, $keyword);
        $this->view->assign('signups', $signups);
        $this->view->assign('page', $page);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('useruid', $useruid);
        $this->view->assign('weeks', $this->weeks);

        //excel export
        if ($this->request->hasArgument('excelExport')) {
            $phpExcelService = GeneralUtility::makeInstanceService('phpexcel');
            $phpExcel = $phpExcelService->getPHPExcel();
            $sheet = $phpExcel->setActiveSheetIndex(0);
            $dataArray[] = ['序号', '活动名称', '活动类别', '活动标签', '活动地点', '活动开始时间', '签到时间'];
            if ($signups->count() > 0) {
                $i = 1;
                foreach ($signups as $signup) {
                    $dataArray[] = [
                        $i++,
                        $signup->getActivityuid()->getName(),
                        $signup->getActivityuid()->getTypes()->getName(),
                        $signup->getActivityuid()->getTagname(),
                        $signup->getActivityuid()->getAddress(),
                        $signup->getActivityuid()->getWay() == 0 ? $signup->getActivityuid()->getSttime()->format('Y-m-d H:i') : $this->weeks[$signup->getActivityuid()->getWeek() - 1]["name"] . $signup->getActivityuid()->getHour(),
                        $signup->getSigntime()->format('Y-m-d H:i:s')
                    ];
                }
                $sheet->fromArray($dataArray, NULL, 'A1');
                $objWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel2007', $phpExcel);
                $fileName = '志愿者参加活动列表_' . date('Y-m-d');
                header('Pragma: public');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Type: application/force-download');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter->save('php://output');
                exit;
            }
        }
    }

    /**
     * action success
     * 
     * @return void
     */
    public function successAction()
    {
        $flag = $this->request->hasArgument('flag') ? $this->request->getArgument('flag') : GeneralUtility::_GP('flag');
        $from = $this->request->hasArgument('from') ? $this->request->getArgument('from') : GeneralUtility::_GP('name');
        $message = $this->request->hasArgument('message') ? $this->request->getArgument('message') : GeneralUtility::_GP('message');
        $this->view->assign('flag', $flag);
        $this->view->assign('from', $from);
        $this->view->assign('message', $message);
    }

    /**
     * 输出json
     * 
     * @param unknown $jsonArr
     */
    public function printJson($jsonArr)
    {
        die(\json_encode($jsonArr, JSON_UNESCAPED_UNICODE));
    }
}
