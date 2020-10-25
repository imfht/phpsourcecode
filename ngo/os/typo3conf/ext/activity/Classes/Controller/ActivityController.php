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
 * ActivityController
 */
class ActivityController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * activityRepository
     * 
     * @var \Jykj\Activity\Domain\Repository\ActivityRepository
     * @inject
     */
    protected $activityRepository = null;

    /**
     * dictitemRepository
     * 
     * @var \Jykj\Dicts\Domain\Repository\DictitemRepository
     * @inject
     */
    protected $dictitemRepository = null;

    /**
     * userRepository
     * 
     * @var \Jykj\User\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository = null;

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

    //活动的状态
    protected $sendStat = [
        ["uid" => 0, "name" => "待发布"],
        ["uid" => 1, "name" => "已发布"],
        ["uid" => 2, "name" => "已下线"]
    ];

    //是否收费
    protected $modeStat = [
        ["uid" => 0, "name" => "免费"],
        ["uid" => 1, "name" => "收费"]
    ];

    //审核状态
    protected $checkStat = [
        ["uid" => 0, "name" => "待审核"],
        ["uid" => 1, "name" => "审核成功"],
        ["uid" => 2, "name" => "审核失败"]
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

    //活动方式
    protected $ways = [
        ["uid" => 0, "name" => "普通活动"],
        ["uid" => 1, "name" => "常态化活动"]
    ];

    var $imgpath = '';
    public function initializeAction()
    {
        $this->imgpath=PATH_site . 'uploads/tx_activity/images/';
        if (!is_dir($this->imgpath)) mkdir($this->imgpath, 0755,true);        
    }
    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        if ($_GET['tx_activity_activity']['@widget_0']['currentPage']) {
            $page = $_GET['tx_activity_activity']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $activities = $this->activityRepository->findAlls($keyword);
        $this->view->assign('activities', $activities);
        $this->view->assign('page', $page);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('sendStat', $this->sendStat);
        $this->view->assign('ways', $this->ways);
        $this->view->assign('weeks', $this->weeks);
    }

    /**
     * action show
     * 
     * @param \Jykj\Activity\Domain\Model\Activity $activity
     * @return void
     */
    public function showAction(\Jykj\Activity\Domain\Model\Activity $activity)
    {
        $this->view->assign('activity', $activity);
    }

    /**
     * action new
     * 
     * @return void
     */
    public function newAction()
    {
        //活动类型
        $dictTypes = $this->dictitemRepository->findAlls("", 1);
        $this->view->assign('dictTypes', $dictTypes);

        //活动标签
        $dictTags = $this->dictitemRepository->findAlls("", 2);
        $this->view->assign('dictTags', $dictTags);
        $this->view->assign('ways', $this->ways);
        $this->view->assign('weeks', $this->weeks);
        $activity = new \Jykj\Activity\Domain\Model\Activity();
        $activity->setWay(0);
        $activity->setSttime(time());
        $activity->setOvertime(time());
        $this->view->assign('activity', $activity);
    }

    /**
     * action create
     * 
     * @param \Jykj\Activity\Domain\Model\Activity $activity
     * @return void
     */
    public function createAction(\Jykj\Activity\Domain\Model\Activity $activity)
    {
        $this->addFlashMessage('活动信息保存成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        if ($activity->getWay() == 0) {
            //赋值开始时间和结束时间
            $sttime = $this->request->hasArgument('sttime') ? $this->request->getArgument('sttime') : '0';
            $overtime = $this->request->hasArgument('overtime') ? $this->request->getArgument('overtime') : '0';
            $activity->setSttime(strtotime($sttime));
            $activity->setOvertime(strtotime($overtime));
            $activity->setWeek(0);
            $activity->setHour("");
        }else if($activity->getWay() == 1) {
            //赋值选择周和时间
            $week = $this->request->hasArgument('week') ? $this->request->getArgument('week') : '0';
            $hour = $this->request->hasArgument('hour') ? $this->request->getArgument('hour') : '';
            $activity->setWeek($week);
            $activity->setHour($hour);
            $date = new \DateTime();
            $date->setTimestamp(0);
            $activity->setSttime($date);
            $activity->setOvertime($date);
        }
        $fname = $_FILES['tx_activity_activity']['name']['pictures'];
        if (!empty($fname)) {
            $filename = md5(uniqid($fname)) . '.' . end(explode('.', $fname));
            if (GeneralUtility::upload_copy_move($_FILES['tx_activity_activity']['tmp_name']['pictures'], $this->imgpath . $filename)) {
                $activity->setPictures($filename);
            }
        }
        $activity->setSenduser($GLOBALS['TSFE']->fe_user->user["uid"]);
        $activity->setSendstat(1);

        //默认活动是发布状态，后续根据业务进行处理
        // $activity->setCrdate(time());
        $this->activityRepository->add($activity);

        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param \Jykj\Activity\Domain\Model\Activity $activity
     * @ignorevalidation $activity
     * @return void
     */
    public function editAction(\Jykj\Activity\Domain\Model\Activity $activity)
    {
        //活动类型
        $dictTypes = $this->dictitemRepository->findAlls("", 1);
        $this->view->assign('dictTypes', $dictTypes);

        //活动标签
        $dictTags = $this->dictitemRepository->findAlls("", 2);
        $this->view->assign('dictTags', $dictTags);
        $this->view->assign('ways', $this->ways);
        $this->view->assign('weeks', $this->weeks);
        if ($activity->getWay() == 1) {
            $activity->setHour(strtotime(date("Y-m-d " . $activity->getHour())));
        }
        $this->view->assign('activity', $activity);
    }

    /**
     * action update
     * 
     * @param \Jykj\Activity\Domain\Model\Activity $activity
     * @return void
     */
    public function updateAction(\Jykj\Activity\Domain\Model\Activity $activity)
    {
        $this->addFlashMessage('活动信息修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        if ($activity->getWay() == 0) {

            //赋值开始时间和结束时间
            $sttime = $this->request->hasArgument('sttime') ? $this->request->getArgument('sttime') : '0';
            $overtime = $this->request->hasArgument('overtime') ? $this->request->getArgument('overtime') : '0';
            $activity->setSttime(strtotime($sttime));
            $activity->setOvertime(strtotime($overtime));
            $activity->setWeek(0);
            $activity->setHour("");
        } else if ($activity->getWay() == 1) {
            //赋值选择周和时间
            $week = $this->request->hasArgument('week') ? $this->request->getArgument('week') : '0';
            $hour = $this->request->hasArgument('hour') ? $this->request->getArgument('hour') : '';
            $activity->setWeek($week);
            $activity->setHour($hour);
            $activity->setSttime(time());
            $activity->setOvertime(time());
        }
        $fname = $_FILES['tx_activity_activity']['name']['pictures'];
        if (!empty($fname)) {
            $imgpath = PATH_site . 'uploads/tx_activity/';
            if (!is_dir($imgpath)) {
                mkdir($imgpath, 0755, true);
            }
            $filename = md5(uniqid($fname)) . '.' . end(explode('.', $fname));
            if (GeneralUtility::upload_copy_move($_FILES['tx_activity_activity']['tmp_name']['pictures'], $imgpath . $filename)) {
                if ($activity->getPictures() != "") {
                    //删除原有文件
                    $ipath = $imgpath . $activity->getPictures();
                    unlink($ipath);
                }
                $activity->setPictures($filename);
            }
        }
        //修改活动内容后,重新生成签到二维码
        if ($activity->getQrcode()!='') {
            $actQrcode = PATH_site.$activity->getQrcode();
            if(file_exists($actQrcode)) unlink($actQrcode);
            $activity->setQrcode('');
        }

        $this->activityRepository->update($activity);

        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param \Jykj\Activity\Domain\Model\Activity $activity
     * @return void
     */
    public function deleteAction(\Jykj\Activity\Domain\Model\Activity $activity)
    {
        $this->addFlashMessage('活动信息删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $activity->setDeltag(1);
        $this->activityRepository->update($activity);

        //$this->activityRepository->remove($activity);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('list');
    }

    /**
     * action download
     * 二维码下载
     * 
     * @return void
     */
    public function downloadAction()
    {
        // 下载二维码
        $actuid = $this->request->hasArgument('activity') ? $this->request->getArgument('activity') : GeneralUtility::_GP('activity');
        $activity = $this->activityRepository->findByUid($actuid);
        $filepath = PATH_site . $activity->getQrcode();
        // http headers
        header('Content-Type: application-x/force-download');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Content-length: ' . filesize($filepath));
        // for IE6
        if (false === strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) {
            header('Cache-Control: no-cache, must-revalidate');
        }
        header('Pragma: no-cache');
        readfile($filepath);
        die;
    }


    /**
     * action qtlist
     * 
     * @return void
     */
    public function qtlistAction()
    {
        $keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        if ($_GET['tx_activity_activity']['@widget_0']['currentPage']) {
            $page = $_GET['tx_activity_activity']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $activities = $this->activityRepository->findAlls($keyword);
        $this->view->assign('activities', $activities);
        $this->view->assign('page', $page);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('sendStat', $this->sendStat);
        $this->view->assign('ways', $this->ways);
        $this->view->assign('weeks', $this->weeks);
    }

    /**
     * action qtshow
     * 
     * @return void
     */
    public function qtshowAction()
    {
        $actuid = $this->request->hasArgument('activity') ? $this->request->getArgument('activity') : GeneralUtility::_GP('activity');
        $signinUri = $this->uriBuilder->reset()->setTargetPageUid($this->settings['signpage'])->setArguments(['activity' => $actuid,'flag'=>'signin'])->uriFor('signup', array(), 'Signup', 'Activity', 'activity');
        $chinkinUri = $this->uriBuilder->reset()->setTargetPageUid($this->settings['checkpage'])->setArguments(['activity' => $actuid,'flag'=>'checkin'])->uriFor('signup', array(), 'Signup', 'Activity', 'activity');
        $activity = $this->activityRepository->findByUid($actuid);
        $this->view->assign('activity', $activity);
        $this->view->assign('signinUri', $signinUri);
        $this->view->assign('chinkinUri', $chinkinUri);
    }

    /**
     * action send
     * 活动发布状态
     * 
     * @param \Jykj\Activity\Domain\Model\Activity $activity
     * @return void
     */
    public function sendAction(\Jykj\Activity\Domain\Model\Activity $activity)
    {
        $stat = $this->request->hasArgument('stat') ? $this->request->getArgument('stat') : '';
        if ($stat == "") {
            $this->addFlashMessage('活动下线失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        } else {
            $this->addFlashMessage('活动下线成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            $activity->setSendstat($stat);
            $this->activityRepository->update($activity);

            //刷新前台缓存
            GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        }
        $this->redirect('list');
    }

    /**
     * action ajax
     * 异步调用
     * 
     * @return void
     */
    public function ajaxAction()
    {
        $flag = GeneralUtility::_GP('flag');
        if ($flag=='generateQRcode') {
            // 生成二维码连接
            $actuid = GeneralUtility::_GP('activity');
            $activity = $this->activityRepository->findByUid($actuid);
            if ($activity->getQrcode()!='') {
                $file = $activity->getQrcode();
            } else {
                $ewmpage = $this->settings['ewmpage'];
                $uri = $this->uriBuilder->reset()->setTargetPageUid($ewmpage)->setArguments(['activity' => $actuid,'flag'=>'checkin'])->uriFor('signup', array(), 'Signup', 'Activity', 'activity');
                // var_dump($uri);
                $data = array(
                    'actuid' => $actuid,
                    'acttime' => date('Y-m-d H:i',$activity->getSttime()).' - '.date('Y-m-d H:i',$activity->getOvertime()),
                    'actname' => $activity->getName(),
                    'actdesc' => trim($activity->getIntroduce()),
                );
                $file = $this->compositePicture($uri,$data);
                $activity->setQrcode($file);
                $this->activityRepository->update($activity);
                $persistenceManager = $this->objectManager->get(PersistenceManager::class);
                $persistenceManager->persistAll();
            }
            echo '<img src="/'.$file.'" id="ewm_act_'.$actuid.'"/>' ;
            die;
        }
    }

    /**
     * action interface
     * 供外部调用接口
     * 
     * @return void
     */
    public function interfaceAction()
    {
        $act = GeneralUtility::_GP('act');
        if ($act == 'list') {

            //正在发布的活动[首页]
            $useruid = GeneralUtility::_GP('useruid');
            $user = $this->userRepository->findByUid($useruid);
            $isreg = 0;

            //0未注册；1已经注册
            if ($user) {
                $isreg = $user->getName() == "" ? 0 : 1;
            }
            $signups = $this->activityRepository->findSignNowActivities(3, 0);
            if (count($signups) > 0) {
                $arrImage = [];

                //图片+uid
                $arrImage = [];

                //简介
                foreach ($signups as $v) {
                    $arrImage[] = ["uid" => $v->getUid(), "imgpath" => $v->getPictures() == "" ? ExtensionManagementUtility::extPath('activity')."Resources/Public/Images/default.png" : GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'uploads/tx_activity/' . $v->getPictures()];
                    $arrData[] = $v->getIntroduce();
                }
                $jsonArr = ['stat' => 0, 'image' => $arrImage, 'data' => $arrData, "isreg" => $isreg];
                self::printJson($jsonArr);
            } else {
                $jsonArr = ['stat' => -3, 'msg' => '亲管理员还没有发布活动或者活动已经过期！', "isreg" => $isreg];
                self::printJson($jsonArr);
            }
        } elseif ($act == 'more') {
            //活动详情[首页]
            $activityuid = GeneralUtility::_GP('activityuid');
            if ($activityuid == "") {
                $jsonArr = ['stat' => -2, 'msg' => '获取数据错误！'];
                self::printJson($jsonArr);
            }
            $activity = $this->activityRepository->findByUid($activityuid);
            if ($activity) {
                $url = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

                //进行img 替换
                $arrText = explode('<img', $activity->getContents());
                $newText = $arrText[0];
                for ($i = 1; $i < count($arrText); $i++) {
                    preg_match('/<\\s*img\\s+[^>]*?src\\s*=\\s*(\'|\\")(.*?)\\1[^>]*?\\/?\\s*>/i', "<img" . $arrText[$i], $match);
                    if (substr($match[2], 0, 7) == "http://" || substr($match[2], 0, 8) == "https://") {
                        $newimg = '<img src="' . $match[2] . '"  style="height:auto;width:100%"/>';
                    } else {
                        $newimg = '<img src="' . $url . $match[2] . '"  style="height:auto;width:100%"/>';
                    }
                    $strt = str_replace($match[0], $newimg, "<img" . $arrText[$i]);
                    $newText .= $strt;
                }
                $arrData = [
                    "uid" => $activity->getUid(),
                    "title" => $activity->getName(),
                    "image" => $activity->getPictures() == "" ? GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . ExtensionManagementUtility::extPath('activity').'Resources/Public/Images/default.png' : GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'uploads/tx_activity/' . $activity->getPictures(),
                    "sendtime" => date("Y-m-d H:i", $activity->getCrdate()),
                    "sttime" => $activity->getWay() == 0 ? $activity->getSttime()->format('Y-m-d H:i') : $this->weeks[$activity->getWeek() - 1]["name"] . $activity->getHour(),
                    "content" => $newText,
                    "address" => $activity->getAddress(),
                    "types" => $activity->getTypes()->getName(),
                    "introduce" => $activity->getIntroduce()
                ];
                $jsonArr = ['stat' => 0, 'data' => $arrData];
                self::printJson($jsonArr);
            } else {
                $jsonArr = ['stat' => -3, 'msg' => '没有查询到活动信息！'];
                self::printJson($jsonArr);
            }
        } elseif ($act == 'offline') {
            // ##每分钟执行一次，检测试听、试讲、排课是否过期
            // */1 * * * * /usr/bin/curl http://dev.jiyikeji.cn/interface/activity?act=offline
            //每分钟执行,活动到期自动下线
            $activities = $this->activityRepository->findOnlineActivity();
            if ($activities->count()>0) {
                foreach ($activities as $key => $activity) {
                    $activity->setSendstat(2);
                    $this->activityRepository->update($activity);
                    $persistenceManager = $this->objectManager->get(PersistenceManager::class);
                    $persistenceManager->persistAll();
                }
            } else {
                $jsonArr = ['stat' => 0, 'msg' => '没有需要处理的数据!'];
                self::printJson($jsonArr);
            }
        } else {
            $jsonArr = ['stat' => -1, 'msg' => '数据接口错误！'];
            self::printJson($jsonArr);
        }
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function generateTmp(array $data)
    {
        $tmppath=PATH_site . 'uploads/tx_activity/templete/';
        if (!is_dir($tmppath)) mkdir($tmppath, 0755,true);

        $tmp_ewm = ExtensionManagementUtility::extPath('activity').'Resources/Public/Images/tmp_ewm.png';
        $font = ExtensionManagementUtility::siteRelPath('siteconfig').'Resources/Public/Fonts/msyh.ttf';
        $baseUri = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
        list($width, $height) = getimagesize($tmp_ewm);
        $filename = $tmppath.'tmp_'.time().'_'.$data['actuid'].'.png';
        $actdesc = $this->getBrText($data['actdesc'],23);
        $param  = "-resize {$width}x{$height} -font ".$font." -fill black ";
        $param .= "-pointsize 18 -draw 'text 430,212 \"{$data['actname']}\" ' ";
        $param .= "-pointsize 18 -draw 'text 430,272 \"{$data['acttime']}\" ' ";
        for ($i=0; $i < count($actdesc); $i++) { 
            $y = 330+($i*26);
            $param .= "-pointsize 16 -draw 'text 430,$y \"{$actdesc[$i]}\" ' ";
        }
        $param .= "-colorspace RGB -quality 80";
        $gifCreator = GeneralUtility::makeInstance('TYPO3\CMS\Core\Imaging\GraphicalFunctions');
        $gifCreator->init();
        $gifCreator->imageMagickExec($tmp_ewm, $filename, $param);
        return $filename;
    }

    /**
     * 生成原始的二维码(生成图片文件)
     *
     * @param string $url
     * @param array $text
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function generateEwm(string $url,$uid)
    {
        $ewmpath=PATH_site . 'uploads/tx_activity/erweima/';
        if (!is_dir($ewmpath)) mkdir($ewmpath, 0755,true);

        //生成二维码
        require_once(ExtensionManagementUtility::extPath('activity') . 'Qrcode/qrcode/phpqrcode.php');
        //二维码内容
        $value = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $url;
        //容错级别
        $errorCorrectionLevel = 'L';
        //生成图片大小
        $matrixPointSize = 4;
        //生成二维码图片
        $filename = $ewmpath.'ewm_'.time().'_'.$uid.'.png';
        $QRcode = new \Jykj\Activity\Qrcode\QRcode();
        $QRcode->png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 3);
        //已经生成的原始二维码图片文件
        $QR = imagecreatefromstring(file_get_contents($filename));
        //输出图片
        imagepng($QR, 'qrcode.png');
        imagedestroy($QR);
        return $filename;
    }

    /**
     * 合成二维码下载图片
     *
     * @param [type] $primaryImg
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function compositePicture($url,$data)
    {
        $imgpath=PATH_site . 'uploads/tx_activity/imgpath/';
        if (!is_dir($imgpath)) mkdir($imgpath, 0755,true);
        $filename = 'act_ewm_'.time().'.png';
        $path_1 = $this->generateTmp($data);//底图
        $path_2 = $this->generateEwm($url,$data['actuid']);//二维码
        $image_1 = imagecreatefromstring(file_get_contents($path_1));
        $image_2 = imagecreatefromstring(file_get_contents($path_2));
        list($w1, $h1, $type1) = getimagesize($path_1);
        list($w2, $h2, $type2) = getimagesize($path_2);
        imagecopymerge($image_1, $image_2, 136, 157, 0, 0, $w2, $h2, 100);
        //将画布保存到指定的文件
        imagepng($image_1,$imgpath.$filename);

        //删除二维码图片和底部图片
        unlink($path_1);
        unlink($path_2);
        $filename = strstr($imgpath.$filename, 'uploads');
        return $filename;
    }

    /**
     * 换行中文显示在图片上
     *
     * @param [type] $text
     * @param [type] $len
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function getBrText($text, $len){
        $text = mb_substr(trim($text), 0, 84);
        if (!preg_match('/^[0-9]+$/', $len) || $len < 1) return FALSE;
    
        $l = mb_strlen($text, 'UTF-8');
        if ($l <= $len) return array($text);
    
        preg_match_all('/.{'.$len.'}|[^\x00]{1,'.$len.'}$/us', $text, $arr);
        return $arr[0];
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
