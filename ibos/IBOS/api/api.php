<?php

use application\core\model\Module;
use application\core\utils\Cache;
use application\core\utils\Env;
use application\core\utils\Ibos;
use application\core\utils\Module as ModuleUtil;
use application\modules\main\model\Setting;
use application\modules\user\model\UserBinding;
use application\modules\role\utils\Role;
use application\modules\user\model\User;
use application\modules\user\model\UserProfile;
use application\modules\position\model\Position;
use application\modules\dashboard\utils\SyncWx;

// 程序根目录路径
define('PATH_ROOT', dirname(__FILE__) . '/../');
$defines = PATH_ROOT . '/system/defines.php';
require_once($defines);
defined('TIMESTAMP') || define('TIMESTAMP', time());
defined('YII_DEBUG') || define('YII_DEBUG', true);
$yii = PATH_ROOT . '/library/yii.php';
require_once($yii);
$mainConfig = require PATH_ROOT . '/system/config/common.php';
Yii::setPathOfAlias('application', PATH_ROOT . DIRECTORY_SEPARATOR . 'system');
Yii::createApplication('application\core\components\Application', $mainConfig);
// 接收信息处理
$result = trim(file_get_contents("php://input"), " \t\n\r");
$signature = Ibos::app()->getRequest()->getQuery('signature');
$timestamp = Ibos::app()->getRequest()->getQuery('timestamp');
$aeskey = Setting::model()->fetchSettingValueByKey('aeskey');
if (strcmp($signature, md5($aeskey . $timestamp)) != 0) {
    Env::iExit("签名错误");
}
if (!empty($result)) {
    $msg = CJSON::decode($result, true);
    switch ($msg['op']) {
        case 'access':
            $return = 'success';
            break;
        case 'version':
            $return = strtolower(implode(',', array(ENGINE, VERSION, VERSION_TYPE)));
            break;
        case 'syncWxuser':
            if (isset($msg['corpid']) && isset($msg['suiteid'])){
                //获得套件允许的授权范围部门
                SyncWx::getInstance()->getAllowDepartment($msg['corpid'], $msg['suiteid']);
                //获得套件允许的授权范围人员
                SyncWx::getInstance()->getAllowUser($msg['corpid'], $msg['suiteid']);
            }
            $return = CJSON::encode(
                array(
                    'isSuccess' => true,
                    'msg' => ''
                )
            );
            break;
        case 'module':
            $returnArray = Ibos::app()->db->createCommand()
                ->select('name,disabled,version,installdate')
                ->from(Module::model()->tableName())
                ->queryAll();
            $return = CJSON::encode($returnArray);
            break;
        case 'installModule':
            if (empty($msg['module'])) {
                $return = CJSON::encode(
                    array(
                        'isSuccess' => false,
                        'msg' => '缺少module参数',
                    )
                );
                break;
            }
            $notInstallModuleArray = ModuleUtil::getNotInstallModule();
            if (empty($notInstallModuleArray)) {
                $return = CJSON::encode(
                    array(
                        'isSuccess' => false,
                        'msg' => '全部模块已经安装',
                    )
                );
                break;
            }
            $moduleArray = is_array($msg['module']) ? $msg['module'] : explode(',', $msg['module']);
            $moduleToInstall = array_intersect($moduleArray, $notInstallModuleArray);
            foreach ($moduleToInstall as $module) {
                ModuleUtil::install($module);
            }
            Cache::update();
            $return = CJSON::encode(
                array(
                    'isSuccess' => true,
                    'msg' => '',
                )
            );
            break;
        case 'bindThird':
            $uid = $msg['uid'];
            $app = $msg['app'];
            $bindValue = $msg['bindValue'];
            $data = array(
                'uid' => $uid,
                'app' => $app,
                'bindvalue' => $bindValue,
            );
            $checkbinding = UserBinding::model()->find(" `uid` = :uid AND `app` = :app", array(':uid' => $uid, ':app' => $app));
            if (empty($checkbinding)) {
                $binding = UserBinding::model()->find(" `bindvalue` = :bindvalue AND `app` = :app", array(':bindvalue' => $bindValue, ':app' => $app));
                if (empty($binding)){
                    $res = UserBinding::model()->add($data);
                }else{
                    UserBinding::model()->deleteAll('bindvalue = :bindvalue AND `app` = :app', array(':bindvalue' => $bindValue, ':app' => $app));
                    $res = UserBinding::model()->add($data);
                }
            } else {
                $res = UserBinding::model()->modify($checkbinding['id'], $data);
            }
            if (isset($msg['user']) && !empty($msg['user'])){
                $user = $msg['user'];
                if (!empty($user['position'])){
                    $position = Position::model()->add(array(
                        'catid' => 1,
                        'posname' => $user['position'],
                    ), true);
                }
                $updateUser = array(
                    'email' => $user['email'],
                    'realname' => $user['name'],
                    'gender' =>$user['gender'] == 1 ? 1 : 0,
                    'positionid' => isset($position) ? $position : 0,
                );
                if (!empty($user['department'])){
                    $user['deptid'] = isset($department[0]) ? (($department['0'] == 1) ? 0 : $department[0]) : 0;
                }else{
                    $updateUser['deptid'] = 0;
                }
                $profile = array('avatar_middle' => $user['avatar']);
                User::model()->updateAll($updateUser, 'uid=:uid', array(':uid' => $uid));
                $userProfile = UserProfile::model()->fetchByPk($uid);
                if (empty($userProfile)){
                    $profile['uid'] = $uid;
                    UserProfile::model()->add($profile);
                }else{
                    UserProfile::model()->modify($uid, $userProfile);
                }
            }
            $return = CJSON::encode(
                array(
                    'isSuccess' => true,
                    'msg' => ''
                )
            );
            break;
        case 'updateAuthority':
            ModuleUtil::updateConfig();
            Role::updateAuthItemByRoleid();
            $return = CJSON::encode(
                array(
                    'isSuccess' => true,
                    'msg' => ''
                )
            );
            break;
        case 'changeContact':
            SyncWx::getInstance()->changeContactByMsgData($msg);
            $return = CJSON::encode(
                array(
                    'isSuccess' => true,
                    'msg' => ''
                )
            );
            break;
        default:
            $return = '不予受理的请求类型';
    }
} else {
    $return = '请求数据不允许为空';
}

header('Content-Type: application/json; charset=' . CHARSET);
exit($return);

