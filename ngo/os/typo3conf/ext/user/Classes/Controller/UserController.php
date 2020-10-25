<?php
namespace Jykj\User\Controller;

/***
 *
 * This file is part of the "用户管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 杨世昌 <yangshichang@ngoos.org>, 极益科技
 *
 ***/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility;

/**
 * UserController
 */
class UserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * userRepository
     *
     * @var \Jykj\User\Domain\Repository\UserRepository
     * @inject
     */
    protected $userRepository = null;

    /**
     * Frontend User Group Repository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
     * @inject
     */
    protected $frontendUserGroupRepository = NULL;
    
    
    /**
     * action password
     * 修改密码界面
     * @return void
     */
    public function passwordAction()
    {
        $userid=$GLOBALS['TSFE']->fe_user->user["uid"];
        $user=$this->userRepository->findByUid($userid);
        $this->view->assign('user', $user);
    }
    
    /**
     * action updatepwd
     * 修改密码功能
     * @return void
     */
    public function updatepwdAction()
    {
        $this->addFlashMessage('密码修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $data=$this->request->getArguments();
        $user=$this->userRepository->findByUid($data["user"]["__identity"]);
        $password=$data["passwords"];
        $user->setPassword($this->generatePassword($password));
        $this->userRepository->update($user);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('password');
        
    }
    
    /**
     * action orginfo
     * 单位信息界面
     * @return void
     */
    public function orginfoAction()
    {
        $userid=$GLOBALS['TSFE']->fe_user->user["uid"];
        $user=$this->userRepository->findByUid($userid);
        $this->view->assign('user', $user);
    }
    
    /**
     * action updateorg
     * 修改单位信息功能
     * @return void
     */
    public function updateorgAction()
    {
        $this->addFlashMessage('用户信息成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $data=$this->request->getArguments();
        $user=$this->userRepository->findByUid($data["user"]["__identity"]);
        $user->setUsername($data["user"]["username"]);
        $user->setName($data["user"]["name"]);
        $user->setEmail($data["user"]["email"]);
        $user->setTelephone($data["user"]["telephone"]);
        $user->setCompany($data["user"]["company"]);
        $user=$this->userRepository->update($user);
        //刷新前台缓存
        GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
        $this->redirect('orginfo');
    }
    
    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
    	$keyword = $this->request->hasArgument('keyword') ? $this->request->getArgument('keyword') : '';
        if ($_GET['tx_user_user']['@widget_0']['currentPage']) {
            $page = $_GET['tx_user_user']['@widget_0']['currentPage'];
        } else {
            $page = 1;
        }
        $users = $this->userRepository->findAlls($keyword);
        $this->view->assign('users', $users);

        $this->view->assign('page', $page);
        $this->view->assign('keyword', $keyword);
    }

    /**
     * action show
     * 
     * @param \Jykj\User\Domain\Model\User $user
     * @return void
     */
    public function showAction(\Jykj\User\Domain\Model\User $user)
    {
    	$this->view->assign('user', $user);
    }

    /**
     * action print
     * 打印界面
     * @param \Jykj\User\Domain\Model\User $user
     * @return void
     */
    public function printAction(\Jykj\User\Domain\Model\User $user)
    {
    	$this->view->assign('user', $user);
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
     * @param \Jykj\User\Domain\Model\User $user
     * @return void
     */
    public function createAction(\Jykj\User\Domain\Model\User $user)
    {
//     	$this->addFlashMessage('用户新增成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
//         $group = $this->request->hasArgument('usergroup') ? $this->request->getArgument('usergroup') : '3';
//         $user->addUsergroup($this->frontendUserGroupRepository->findByUid($group));
//     	$user->setPassword($this->generatePassword("123456"));
//     	$user->setHeadimgurl("default.jpg");
//     	$user->setCrdate(time());//插入时间
//     	$this->userRepository->add($user);
// 		//刷新前台缓存
// 		GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
//         $this->redirect('list');
    }

    /**
     * action edit
     *
     * @return void
     */
    public function editAction()
    {
    }

    /**
     * action update
     *
     * @param \Jykj\User\Domain\Model\User $user
     * @return void
     */
    public function updateAction(\Jykj\User\Domain\Model\User $user)
    {
//         $this->addFlashMessage('用户修改成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
//         $group = $this->request->hasArgument('usergroup') ? $this->request->getArgument('usergroup') : '3';
//         foreach($user->getUsergroup() as $v){
//         	$user->removeUsergroup($this->frontendUserGroupRepository->findByUid($v));
//         }
//         $user->addUsergroup($this->frontendUserGroupRepository->findByUid($group));
//         $this->userRepository->update($user);
// 		//刷新前台缓存
// 		GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
//         $this->redirect('list');
    }

    /**
     * action delete
     *
     * @return void
     */
    public function deleteAction()
    {
//     	$uid=$this->request->getArgument("user");
//     	$user=$this->userRepository->findUserInfo($uid);
    	
//     	$this->addFlashMessage('用户信息删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
//         $this->userRepository->remove($user);
// 		//刷新前台缓存
// 		GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
//         $this->redirect('list');
    }
    
    /**
     * action changepwd
     * 重置密码
     * @return void
     */
    public function changepwdAction(){
    	$uid=$this->request->getArgument("user");
    	$user=$this->userRepository->findUserInfo($uid);
    	 
    	$this->addFlashMessage('用户密码重置成功，默认密码为“123456”', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
    	$user->setPassword($this->generatePassword("123456"));
    	$this->userRepository->update($user);
    	$this->redirect('list');
    }
    
    /**
     * jquery validator 校验
     * 验证用户名、邮箱、电话是否重复
     */
    public function ajaxdataAction()
    {
        $act=GeneralUtility::_GP('act');
        $uid=GeneralUtility::_GP('uid');//新增uid=0,修改uid为对应的id值
        if($act=="checkUserName"){
            //登录用户名不能重复
            $username=GeneralUtility::_GP('username');
            $iRet = $this->userRepository->checkExist($username,"","",$uid);
            if($iRet==0){
                print "true";
            }else{
                print "false";
            }
            exit();
        }else if($act=="checkEmail"){
            //邮箱不能重复
            $email=GeneralUtility::_GP('email');
            $iRet = $this->userRepository->checkExist("",$email,"",$uid);
            if($iRet==0){
                print "true";
            }else{
                print "false";
            }
            exit();
        }else if($act=="checkTelephone"){
            //手机号不能重复
            $telephone=GeneralUtility::_GP('telephone');
            $iRet = $this->userRepository->checkExist("","",$telephone,$uid);
            if($iRet==0){
                print "true";
            }else{
                print "false";
            }
            exit();
        }else if($act=="checkPassword"){
            //检测密码是否
            $user=$this->userRepository->findByUid($uid);
            $oldpassword=GeneralUtility::_GP('oldpassword');
            $bool = $this->checkPassword($oldpassword,$user->getPassword());
            if ($bool) {
                print "true";
            } else {
                print "false";
            }
            exit();
        }else{
            die("请求失败！");
        }
    }
    
    /**
     * 加密原密码
     * @param	string		$password
     * @param	array		$arrGenerate
     * @return	string		$arrPassword
     */
    public static function generatePassword($password, $arrGenerate = array()) {
        $arrPassword = array();
        
        // Uebergebenes Password setzten.
        // Hier wird kein strip_tags() o.Ae. benoetigt, da beim schreiben in die Datenbank immer "$GLOBALS['TYPO3_DB']->fullQuoteStr()" ausgefuehrt wird!
        $arrPassword['normal'] = trim($password);
        
        // Erstellt ein Password.
        if ($arrGenerate['mode']) {
            $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHIKLMNPQRSTUVWXYZ';
            
            $arrPassword['normal'] = '';
            
            for ($i = 0; $i < (($arrGenerate['length']) ? $arrGenerate['length'] : 8); $i++) {
                $arrPassword['normal'] .= $chars{mt_rand(0, strlen($chars))};
            }
        }
        
        // Unverschluesseltes Passwort uebertragen.
        $arrPassword['encrypted'] = $arrPassword['normal'];
        
        // Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password verschluesselt.
        if (ExtensionManagementUtility::isLoaded('saltedpasswords') && $GLOBALS['TYPO3_CONF_VARS']['FE']['loginSecurityLevel']) {
            $saltedpasswords = SaltedPasswordsUtility::returnExtConf();
            
            if ($saltedpasswords['enabled']) {
                $tx_saltedpasswords = GeneralUtility::makeInstance($saltedpasswords['saltedPWHashingMethod']);
                
                $arrPassword['encrypted'] = $tx_saltedpasswords->getHashedPassword($arrPassword['normal']);
            }
        }
        
        if ($GLOBALS['TYPO3_CONF_VARS']['FE']['passwordHashing']['className']) {
            $arrPassword['encrypted'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\PasswordHashFactory')->getDefaultHashInstance('FE')->getHashedPassword($arrPassword['normal']);
        }
        
        return $arrPassword['encrypted'];
    }

    /**
     * 密码修改比较
     *
     * @param	string		$submittedPassword 明文
     * @param	string		$originalPassword 加密后的密码
     * @return	boolean		$check
     */
    public static function checkPassword($submittedPassword, $originalPassword) {
        $check = FALSE;
        
        // Wenn "saltedpasswords" installiert ist wird deren Konfiguration geholt, und je nach Einstellung das Password ueberprueft.
        if (ExtensionManagementUtility::isLoaded('saltedpasswords') && $GLOBALS['TYPO3_CONF_VARS']['FE']['loginSecurityLevel']) {
            $saltedpasswords = SaltedPasswordsUtility::returnExtConf();
            
            if ($saltedpasswords['enabled']) {
                $tx_saltedpasswords = GeneralUtility::makeInstance($saltedpasswords['saltedPWHashingMethod']);
                
                $check = $tx_saltedpasswords->checkPassword($submittedPassword, $originalPassword);
            }
        }
        
        if ($GLOBALS['TYPO3_CONF_VARS']['FE']['passwordHashing']['className']) {
            $check = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\PasswordHashFactory')->get($originalPassword, 'FE')->checkPassword($submittedPassword, $originalPassword);
        }
        
        return $check;
    }
    
    
    /**
     * 基本的http get请求
     *
     * @param string $url
     * @return string
     */
    protected function _httpGet($url)
    {
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 对认证证书来源的检查
        \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        // 从证书中检查SSL加密算法是否存在
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = \curl_exec($ch);
        \curl_close($ch);
        return $output;
    }
    
    /**
     * 通过生日计算年龄
     * @param string $birthday yyyy-mm-dd
     */
    public function birthday2Age($birthday){
		$age = strtotime($birthday);
		if($age === false){
			return false;
		}
		list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age));
		$now = strtotime("now");
		list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now));
		$age = $y2 - $y1;
		if((int)($m2.$d2) < (int)($m1.$d1)){
			$age -= 1;
		}
		return $age;
	} 
	
}
