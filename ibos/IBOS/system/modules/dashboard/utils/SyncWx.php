<?php
/**
 * @link http://api.ibos.cn/
 * @copyright Copyright (c) 2017 IBOS Inc
 */

namespace application\modules\dashboard\utils;

use application\core\model\Log;
use application\core\utils\ArrayUtil;
use application\core\utils\Cache;
use application\core\utils\Ibos;
use application\core\utils\Org;
use application\core\utils\StringUtil;
use application\core\utils\WebSite;
use application\modules\department\model\Department;
use application\modules\department\model\DepartmentBinding;
use application\modules\main\model\Setting;
use application\modules\message\core\co\CoApi;
use application\modules\message\core\wx\WxApi;
use application\modules\position\model\Position;
use application\modules\user\model\User;
use application\modules\user\model\UserBinding;
use application\modules\user\model\UserProfile;
use application\modules\user\utils\User as UserUtil;

class SyncWx
{


    public static function getInstance()
    {
        return new static();
    }

    /**
     * 通过类型创建访问官网的url，以便官网调用微信接口
     *
     * @param string $type 对应官网访问微信接口的方法名
     * @return string url
     */
    private function createUrlByType($type)
    {
        $aeskey = Wx::getInstance()->getAeskey();
        $url = WebSite::getInstance()->build('Api/Wxsync/' . $type, array('aeskey' => $aeskey));
        return $url;
    }

    private function getWxAndIbosDeptBinding()
    {
        $dept = Ibos::app()->db->createCommand()
            ->select('deptid,bindvalue')
            ->from('{{department_binding}}')
            ->where(" `app` = 'wxqy' ")
            ->queryAll();
        $deptRelated = array();
        // 键 => 值 ：IBOS 部门id => 微信部门id
        if (!empty($dept)) {
            foreach ($dept as $d) {
                $deptRelated[$d['deptid']] = $d['bindvalue'];
            }
        }
        $deptRelated[0] = 1;
        return $deptRelated;
    }

    /**
     * 判断是否绑定企业微信和是否授权通讯录
     * @return array
     */
    public function checkBindingWxAndAuthContact()
    {
        $return = array();
        $aeskey = Setting::model()->fetchSettingValueByKey('aeskey');
        $url = 'Api/WxCorp/isBinding';
        $res = WebSite::getInstance()->fetch($url, array('aeskey' => $aeskey));
        if (is_array($res)) {
            return array(
                'isBindingWx' => false,
                'isContacntAuth' => false
            );
        }
        $result = \CJSON::decode($res, true);
        $return['isBindingWx'] = $result['type'] == 1 ? true : false;
        $wxPermission = CoApi::getInstance()->getWxPermission();
        $return['isContacntAuth'] = ArrayUtil::getValue($wxPermission, 'data.canWrite', false);
        return $return;
    }

    public function addWxDept($deptid)
    {
        $check = $this->checkBindingWxAndAuthContact();
        if ($check['isBindingWx'] && $check['isContacntAuth']) {
            $deptment = Department::model()->fetchByPk($deptid);
            $bindind_dept = DepartmentBinding::model()->getBindingValueForDeptidAndApp($deptment['pid']);
            $bindind_dept = empty($bindind_dept) ? 1 : $bindind_dept;
            $addDeptUrl = $this->createUrlByType('syncDept');
            $res = WxApi::getInstance()->createDept($deptment['deptname'], $bindind_dept, $deptment['sort'], $addDeptUrl);
            if ($res['isSuccess'] && isset($res['data']['id'])) {
                DepartmentBinding::model()->add(array(
                    'deptid' => $deptid,
                    'bindvalue' => $res['data']['id'],
                    'app' => 'wxqy'
                ));
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delWxDept($deptid)
    {
        $check = $this->checkBindingWxAndAuthContact();
        if ($check['isBindingWx'] && $check['isContacntAuth']) {
            $bindvalue = DepartmentBinding::model()->getBindingValueForDeptidAndApp($deptid);
            $url = $this->createUrlByType('delDept');
            $res = WxApi::getInstance()->delDept($bindvalue, $url);
            if ($res) {
                DepartmentBinding::model()->deleteAll('deptid = :deptid AND app = :app', array(':deptid' => $deptid, ':app' => 'wxqy'));
            }
            return $res;
        } else {
            return false;
        }
    }

    public function updateWxDept($deptid)
    {
        $check = $this->checkBindingWxAndAuthContact();
        if ($check['isBindingWx'] && $check['isContacntAuth']) {
            $deptment = Department::model()->fetchByPk($deptid);
            $curDeptBindingValue = DepartmentBinding::model()->getBindingValueForDeptidAndApp($deptid);
            $parentBindValue = DepartmentBinding::model()->getBindingValueForDeptidAndApp($deptment['pid']);
            $parentBindValue = empty($parentBindValue) ? 1 : $parentBindValue;
            $url = $this->createUrlByType('updateDept');
            $res = WxApi::getInstance()->updateDept($curDeptBindingValue, $deptment['deptname'], $parentBindValue, $deptment['sort'], $url);
            return $res;
        } else {
            return false;
        }
    }

    public function addWxUser($userid)
    {
        $check = $this->checkBindingWxAndAuthContact();
        if ($check['isBindingWx'] && $check['isContacntAuth']) {
            $user = User::model()->fetchByUid($userid, true, true); // 强制生成缓存
//            if ($user['status'] == 0){
            // todo 这里不能单纯用状态判断是否这个微信人员被添加过
//                return false;
//            }
            $deptIdRelated = $this->getWxAndIbosDeptBinding();
            $wxDeptIdArr = array();
            foreach (explode(',', $user['alldeptid']) as $deptid) {
                if (isset($deptIdRelated[$deptid])) {
                    $wxDeptIdArr[] = $deptIdRelated[$deptid];
                }
            }
            // 如果并没有找到部门关系，直接放在有权限的顶级部门下
            $deptIdStr = implode(',', $wxDeptIdArr);
            $wxuser = array(
                'uid' => $userid,
                'userid' => $user['mobile'],
                'gender' => ($user['gender'] == 1) ? 1 : 2,
                'deptid' => empty($deptIdStr) ? 1 : $deptIdStr,
                'telephone' => $user['telephone'],
                'realname' => $user['realname'],
                'posname' => $user['posname'],
                'email' => $user['email'],
                'weixin' => $user['weixin'],
                'mobile' => $user['mobile'],
            );
            $url = $this->createUrlByType('syncUser');
            $res = WxApi::getInstance()->createUser($wxuser, $url);
//            if ($res == ''){
//                UserBinding::model()->addUserBinding(array(
//                    'uid' => $user['uid'],
//                    'bindvalue' => $user['mobile'],
//                    'app' => 'wxqy'
//                ));
//            }
            return $res;
        } else {
            return false;
        }
    }

    public function updateWxUser($userid)
    {
        $check = $this->checkBindingWxAndAuthContact();
        if ($check['isBindingWx'] && $check['isContacntAuth']) {
            $bindvalue = UserBinding::model()->fetchBindValueByUidAndApp($userid, 'wxqy');
            $user = User::model()->fetchByUid($userid, true, true); // 强制生成缓存
            $deptIdRelated = $this->getWxAndIbosDeptBinding();
            $wxDeptIdArr = array();
            foreach (explode(',', $user['alldeptid']) as $deptid) {
                if (isset($deptIdRelated[$deptid])) {
                    $wxDeptIdArr[] = $deptIdRelated[$deptid];
                }
            }
            // 如果并没有找到部门关系，直接放在有权限的顶级部门下
            $deptIdStr = implode(',', $wxDeptIdArr);
            $wxuser = array(
                'userid' => $bindvalue,
                'gender' => ($user['gender'] == 1) ? 1 : 2,
                'deptid' => empty($wxDeptIdArr) ? 1 : $deptIdStr,
                'telephone' => $user['telephone'],
                'realname' => $user['realname'],
                'posname' => $user['posname'],
                'email' => $user['email'],
                'weixin' => $user['weixin'],
                'mobile' => $user['mobile'],
                'enable' => ($user['status'] == 0) ? 1 : 0,
            );
            $url = $this->createUrlByType('updateUser');
            $res = WxApi::getInstance()->updateUser($wxuser, $url);
            return $res;
        } else {
            return false;
        }
    }

    /**
     * 批量删除微信用户
     * @param $uids
     * @return bool
     */
    public function batchDeleteWxUserByUids($uids)
    {
        $uidsArr = is_array($uids) ? $uids : explode(',', $uids);
        $check = $this->checkBindingWxAndAuthContact();
        if ($check['isBindingWx'] && $check['isContacntAuth']) {
            $bindValues = Ibos::app()->db->createCommand()
                ->from(UserBinding::model()->tableName())
                ->select('bindvalue')
                ->where(sprintf("`uid` IN (%s) AND `app` = '%s'", "'" . implode("','", $uidsArr) . "'", 'wxqy'))
                ->queryColumn();
            $url = $this->createUrlByType('batchDelUser');
            $res = WxApi::getInstance()->batchDelUserByBindValues($bindValues, $url);
            if ($res == '') {
                UserBinding::model()->deleteAll(sprintf("`uid` IN (%s) AND `app` = '%s'", "'" . implode("','", $uidsArr) . "'", 'wxqy'));
            }
            return true;
        } else {
            return false;
        }
    }

    public function delWxUser($userid)
    {
        $check = $this->checkBindingWxAndAuthContact();
        if ($check['isBindingWx'] && $check['isContacntAuth']) {
            $bindvalue = UserBinding::model()->fetchBindValue($userid, 'wxqy');
            $url = $this->createUrlByType('delUser');
            $user = array('userid' => $bindvalue);
            $res = WxApi::getInstance()->delUser($user, $url);
            if ($res == '') {
                UserBinding::model()->deleteAll('uid=:uid', array(':uid' => $userid));
            }
            return $res;
        } else {
            return false;
        }
    }

    /**
     * 企业微信允许的部门范围
     * @param $corpid
     */
    public function getAllowDepartment($corpid, $suiteid)
    {
        try {
            $res = WebSite::getInstance()->fetch('Wxapi/Api/getWxDeptList', array('corpid' => $corpid, 'suiteid' => $suiteid), 'post');

            SyncWx::getInstance()->syncWxLog(
                array(
                    'method' => __METHOD__ .' '. __LINE__,
                    'action' => 'getWxDeptList',
                    'getWxDeptList' => $res,
                    ),
                2);
            if (!is_array($res)) {
                $result = json_decode($res, true);
                if ($result['errcode'] == 0 && !empty($result['department'])) {
                    $wxDeptments = $result['department'];
                    $deptids = ArrayUtil::getColumn($wxDeptments, 'id');
//                    $this->deleteIbosDepartmentAndUserByWxDpetids($deptids);
                    $insertDeptid = array();
                    foreach ($wxDeptments as $wxDeptment) {
                        //排除微信返回的总公司，IBOS这边不需要
                        if ($wxDeptment['parentid'] != 0) {
                            $binding = DepartmentBinding::model()->fetch('bindvalue = :bindvalue AND app = :app',
                                array(
                                    ':bindvalue' => $wxDeptment['id'],
                                    ':app' => 'wxqy'
                                ));
                            SyncWx::getInstance()->syncWxLog(
                                array(
                                    'method' => __METHOD__ .' '. __LINE__,
                                    'action' => 'getDeptbinding',
                                    'binding' => $binding,
                                ),
                                2);
                            if (empty($binding)) {
                                $ibosDeptment = array(
                                    'deptname' => $wxDeptment['name'],
                                    'pid' => ($wxDeptment['parentid'] == 1 || !in_array($wxDeptment['parentid'], $deptids)) ? 0 : $wxDeptment['parentid'],//这里因为企业微信的顶级部门为1，而ibos的为0
                                    'sort' => $wxDeptment['order']
                                );
                                $deptid = Department::model()->add($ibosDeptment, true);
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'addIbosDept',
                                        'ibosDeptment' => $ibosDeptment,
                                    ),
                                    2);
                                $insertDeptid[] = $deptid;
                                $bindDepartment = array(
                                    'deptid' => $deptid,
                                    'bindvalue' => $wxDeptment['id'],
                                    'app' => 'wxqy'
                                );
                                DepartmentBinding::model()->add($bindDepartment, true);
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'addIbosDeptBing',
                                        'bindDepartment' => $bindDepartment,
                                    ),
                                    2);
                            }
                        }
                    }
                    //更新部门的上级id
                    if (!empty($insertDeptid)) {
                        $pids = $this->getDepartmentPidByDeptid($insertDeptid);
                        if (!empty($pids)) {
                            foreach ($pids as $pid) {
                                $deptid = DepartmentBinding::model()->getDeptidByBindingValueAndApp($pid);
                                $insertDeptidStr = ArrayUtil::formatArrayForSearchInByArray($insertDeptid);
                                Department::model()->updateAll(array('pid' => $deptid), "pid = :pid AND deptid IN ({$insertDeptidStr})", array(':pid' => $pid));
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'updateDeptPid',
                                        'oldPid' => $pid,
                                        'newPid' => $deptid,
                                        'insertDeptidStr' => $insertDeptidStr,
                                    ),
                                    2);
                            }
                        }
                    }
                    UserUtil::CacheUser();
                    Org::update();
                    Cache::update('setting');
                }
            }
        } catch (\Exception $e) {
            \Yii::log($e->getMessage(), \CLogger::LEVEL_ERROR, 'getallowdepartment');
        }
    }

    /**
     * 获得企业微信的部门绑定值
     * @return array
     */
    protected function getWxDeptBindingValue()
    {
        $values = Ibos::app()->db->createCommand()
            ->select('bindvalue')
            ->from(DepartmentBinding::model()->tableName())
            ->where('app = :app', array(':app' => 'wxqy'))
            ->queryColumn();
        return $values;
    }

    /**
     * 删除IBOS部门和用户,逻辑是拿到企业微信的部门绑定值和ibos的绑定值进行对比，如果ibos的绑定值没有在企业微信的绑定值
     * 则应该删除IBOS部门及其下属部门，同时应该删除部门绑定关系，再者禁用这些部门下的用户，删除绑定关系,其实这种方法是有
     * 一种说不出来的问题的，留给以后优化吧
     * @param $deptids
     */
    protected function deleteIbosDepartmentAndUserByWxDpetids($deptids)
    {
        $bindDepts = $this->getWxDeptBindingValue();
        foreach ($bindDepts as $bindDept) {
            if (!in_array($bindDept, $deptids)) {
                $deptid = DepartmentBinding::model()->getDeptidByBindingValueAndApp($bindDept);
                $subDeptids = Department::model()->fetchDeptAllChildOfChildByDeptID($deptid);
                $delDeptids = array_unique(array_merge($subDeptids, array($deptid)));
                $delDeptidsStr = ArrayUtil::formatArrayForSearchInByArray($delDeptids);
                Department::model()->deleteAll("deptid IN ({$delDeptidsStr})");
                DepartmentBinding::model()->deleteAll("deptid IN ({$delDeptidsStr}) AND app = 'wxqy'");
                $uids = Ibos::app()->db->createCommand()
                    ->select('uid')
                    ->from(User::model()->tableName())
                    ->where("deptid IN ({$delDeptidsStr})")
                    ->queryColumn();
                if (!empty($uids)) {
                    $uidsStr = ArrayUtil::formatArrayForSearchInByArray($uids);
                    User::model()->updateAll(array('status' => 2, 'deptid' => 0), "uid IN ({$uidsStr})");
                    UserBinding::model()->deleteAll("uid IN ({$uidsStr}) AND app = 'wxqy'");
                }
            }
        }
    }

    /**
     * 企业微信允许的成员范围
     * @param $corpid
     */
    public function getAllowUser($corpid, $suiteid)
    {
        try {
            $res = WebSite::getInstance()->fetch('Wxapi/Api/getWxUserList', array('corpid' => $corpid, 'suiteid' => $suiteid), 'post');
            SyncWx::getInstance()->syncWxLog(
                array(
                    'method' => __METHOD__ .' '. __LINE__,
                    'action' => 'getWxUserList',
                    'wxUserList' => $res,
                ),
                2);
            if (!is_array($res)) {
                $result = json_decode($res, true);
                if ($result['isSuccess'] == true && !empty($result['data'])) {
                    $users = $result['data'];
                    $wxUsers = array();
                    foreach ($users as $user) {
                        $wxUsers[$user['userid']] = $user;
                    }
                    $userids = ArrayUtil::getColumn($users, 'userid');
                    $values = UserBinding::model()->getAllBindValueByApp();
                    $noDelAndAddBindValue = array_intersect($userids, $values);
                    $addUidsBindvalues = array_diff($userids, $noDelAndAddBindValue);
                    $delUidsBindvalue = array_diff($values, $noDelAndAddBindValue);
                    if (!empty($delUidsBindvalue)) {//禁用用户，删除绑定
                        $uids = $this->getUidsByBindValues($delUidsBindvalue);
                        $allAdminUids = $this->getAllAmdinUids();
                        // todo 不能全部删除 admin 账号
                        $adminNeedDel = array_intersect($uids, $allAdminUids);
                        // 如果都要删除，那么留下第一个 ennnnn ...
                        if (count($adminNeedDel) == count($allAdminUids)) {
                            $fastUid = array_shift($allAdminUids);
                            unset($uids[array_search($fastUid, $uids)]); // 留下第一个 admin
                        }
                        if (!empty($uids)) {
                            $uidsStr = ArrayUtil::formatArrayForSearchInByArray($uids);
                            $delUidsBindvalueStr = ArrayUtil::formatArrayForSearchInByArray($delUidsBindvalue);
                            User::model()->updateAll(array('status' => 2), "uid IN ({$uidsStr})");
                            SyncWx::getInstance()->syncWxLog(
                                array(
                                    'method' => __METHOD__ .' '. __LINE__,
                                    'action' => 'delUsers',
                                    'delUsers' => $uidsStr,
                                ),
                                2);
                            UserBinding::model()->deleteAll("bindvalue IN ({$delUidsBindvalueStr}) AND app = 'wxqy'");
                            SyncWx::getInstance()->syncWxLog(
                                array(
                                    'method' => __METHOD__ .' '. __LINE__,
                                    'action' => 'deluserBind',
                                    'delUidsBindvalueStr' => $delUidsBindvalueStr,
                                ),
                                2);
                        }
                    }
                    foreach ($addUidsBindvalues as $addUidsBindvalue) {
                        if (isset($wxUsers[$addUidsBindvalue])) {//容错
                            $wxuser = $wxUsers[$addUidsBindvalue];
                            if (!empty($wxuser['mobile'])) {
                                $userFormobile = User::model()->fetch('mobile = :mobile', array(':mobile' => $wxuser['mobile']));
                            }
                            $userForWxuserId = User::model()->fetch('username = :wxuserid', array(':wxuserid' => $wxuser['userid']));
                            if (!empty($userFormobile)) {
                                User::model()->updateAll(array('status' => 0), 'uid=:uid', array(':uid' => $userFormobile['uid']));
                                $uid = $userFormobile['uid'];
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'enabledUser',
                                        'delUidsBindvalueStr' => $uid,
                                    ),
                                    2);
                            } else if (!empty($userForWxuserId)) {
                                User::model()->updateAll(array('status' => 0), 'uid=:uid', array(':uid' => $userForWxuserId['uid']));
                                $uid = $userForWxuserId['uid'];
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'enabledUser',
                                        'delUidsBindvalueStr' => $uid,
                                    ),
                                    2);
                            } else {
                                $uid = $this->addUserByWxuserData($wxuser);
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'AddIbosUser',
                                        'user' => $wxuser,
                                    ),
                                    2);
                            }
                            $isBinding = UserBinding::model()->isBindingForBindingValue($wxuser['userid']);
                            $haveUidBinding = UserBinding::model()->fetch('uid = :uid And app = :app', array(':uid' => $uid, ':app' => 'wxqy'));
                            if (!$isBinding && empty($haveUidBinding)) {
                                $userBinding = array(
                                    'uid' => $uid,
                                    'bindvalue' => $wxuser['userid'],
                                    'app' => 'wxqy'
                                );
                                UserBinding::model()->addUserBinding($userBinding);
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'AddIbosUserBinding',
                                        'userBinding' => $userBinding,
                                    ),
                                    2);
                            }
                        }
                    }
                    if (!empty($noDelAndAddBindValue)) {
                        foreach ($noDelAndAddBindValue as $value) {
                            if (isset($wxUsers[$value])) {
                                $uid = UserBinding::model()->fetchUidByValue($value);
                                $wxuser = $wxUsers[$value];
                                $params = array(
                                    'gender' => ($wxuser['gender'] == 1) ? 0 : 1,
                                    'deptid' => $this->getDeptid($wxuser['department']),
                                );
                                if (!empty($wxuser['mobile'])) {
                                    // 原来给过就不要覆盖为空了
                                    $params['mobile'] = $wxuser['mobile'];
                                }
                                if (!empty($wxuser['email'])) {
                                    $params['email'] = $wxuser['email'];
                                }
                                User::model()->updateAll($params, 'uid = :uid', array(':uid' => $uid));
                                SyncWx::getInstance()->syncWxLog(
                                    array(
                                        'method' => __METHOD__ .' '. __LINE__,
                                        'action' => 'UpdateGenderANDDeptid',
                                        'updateParams' => $params,
                                        'uid' => $uid
                                    ),
                                    2);
                            }
                        }
                    }
                    UserUtil::CacheUser();
                    Org::update();
                    Cache::update('setting');
                }
            }
        } catch (\Exception $e) {
            \Yii::log($e->getMessage(), \CLogger::LEVEL_ERROR, 'getallowuser');
        }
    }

    /**
     * @param $bindvalues
     * @return array|\CDbDataReader
     */
    protected function getUidsByBindValues($bindvalues)
    {
        $bindvaluesStr = ArrayUtil::formatArrayForSearchInByArray($bindvalues);
        $uids = Ibos::app()->db->createCommand()
            ->select('uid')
            ->from(UserBinding::model()->tableName())
            ->where("bindvalue IN ({$bindvaluesStr}) AND app = 'wxqy'")
            ->queryColumn();
        return $uids;
    }

    /**
     * 获取所有未禁用的 admin 账号，用于留下管理员
     * @param $bindvalues
     * @return array|\CDbDataReader
     */
    protected function getAllAmdinUids()
    {
        $uids = Ibos::app()->db->createCommand()
            ->select('uid')
            ->from(User::model()->tableName())
            ->where("isadministrator = 1 AND status = 0")
            ->queryColumn();
        return $uids;
    }

    /**
     * 用微信用户信息创建ibos用户
     * @param $user
     */
    protected function addUserByWxuserData($user)
    {
        $salt = StringUtil::random(6);
        //默认密码是123456
        $password = md5(md5('123456') . $salt);
        $addUsers = array(
            'realname' => $user['name'],
            'username' => $user['name'],
            'deptid' => $this->getDeptid($user['department']),
            'positionid' => $this->addPositionForName($user['position']),
            'roleid' => 3,//默认给普通成员权限
            'mobile' => isset($user['mobile']) ? $user['mobile'] : '',
            'email' => isset($user['email']) ? $user['email'] : '',
            'gender' => ($user['gender'] == 1) ? 1 : 0,
            'createtime' => time(),
            'guid' => StringUtil::createGuid(),
            'salt' => $salt,
            'password' => $password
        );
        $uid = User::model()->add($addUsers, true);
        Ibos::app()->db->createCommand()
            ->insert('{{user_count}}', array('uid' => $uid));
        $ip = Ibos::app()->request->userHostAddress;
        Ibos::app()->db->createCommand()
            ->insert('{{user_status}}', array('uid' => $uid, 'regip' => $ip, 'lastip' => $ip));
        $addProfile = array(
            'uid' => $uid,
            'telephone' => isset($user['telephone']) ? $user['telephone'] : '',
            'avatar_middle' => $user['avatar'],
        );
        UserProfile::model()->add($addProfile);
        return $uid;
    }

    /**
     * 获得岗位id
     * @param $name
     * @return bool
     */
    protected function addPositionForName($name)
    {
        $positionid = Ibos::app()->db->createCommand()
            ->select('positionid')
            ->from(Position::model()->tableName())
            ->where('posname = :posname', array(':posname' => $name))
            ->queryScalar();
        if (!empty($positionid)) {
            return $positionid;
        } else {
            if (!empty($name)) {
                $positionid = Position::model()->add(array(
                    'catid' => 1,
                    'posname' => $name
                ), true);
                return $positionid;
            } else {
                return '';
            }
        }
    }

    /**
     * 获得部门id
     * @param $deptids
     * @return int
     */
    protected function getDeptid($deptids)
    {
        if (!empty($deptids)) {
            $deptids = is_array($deptids) ? $deptids : explode(',', $deptids);
            if (isset($deptids[0])) {
                $deptBind = DepartmentBinding::model()->fetch(array('select' => '*', 'condition' => sprintf("bindvalue = '%s' AND app = '%s'", $deptids[0], 'wxqy')));
                if (empty($deptBind) || $deptids[0] == 1) {
                    return 0;
                } else {
                    return $deptBind['deptid'];
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * 获得ibos部门的所有上级id
     * @return array
     */
    protected function getDepartmentPidByDeptid($deptid)
    {
        $deptidStr = ArrayUtil::formatArrayForSearchInByArray($deptid);
        $pids = Ibos::app()->db->createCommand()
            ->selectDistinct('pid')
            ->from(Department::model()->tableName())
            ->where("pid != 0 AND deptid IN ({$deptidStr})")
            ->queryColumn();
        return $pids;
    }

    /**
     * 企业微信变更通讯录
     * @param $msgData
     */
    public function changeContactByMsgData($msgData)
    {
        SyncWx::getInstance()->syncWxLog(
            array(
                'method' => __METHOD__ .' '. __LINE__,
                'action' => $msgData['ChangeType'],
                'msgData' => $msgData,
            ),
            2);
        switch ($msgData['ChangeType']) {
            case 'create_party':
                $this->createPartyByMsgData($msgData);
                break;
            case 'update_party':
                $this->updatePartyByMsgData($msgData);
                break;
            case 'delete_party':
                $this->deletePartyById($msgData['Id']);
                break;
            case 'create_user':
                $uid = $this->createUserByMsgData($msgData);
                $this->cacheUser($uid);
                break;
            case 'update_user':
                $uid = $this->updateUserByMsgData($msgData);
                $this->cacheUser($uid);
                break;
            case 'delete_user':
                $uid = UserBinding::model()->fetchUidByValue($msgData['UserID']);
                User::model()->updateAll(array('status' => 2), 'uid = :uid', array(':uid' => $uid));
                UserBinding::model()->deleteBinding($uid);
                $this->cacheUser($uid);
                break;
            default:
                break;
        }
        $this->orgCacheUpdate();
        $this->settingCacheUpate();
    }

    /**
     * 标记更新setting表
     */
    protected function settingCacheUpate()
    {
        if (!Ibos::app()->cache->get('setting-cache-update')) {
            Ibos::app()->cache->set('setting-cache-update', true);
        }
    }

    /**
     * 标记更新文件缓存
     */
    protected function orgCacheUpdate()
    {
        if (!Ibos::app()->cache->get('org-update')) {
            Ibos::app()->cache->set('org-update', true);
        }
    }

    /**
     * 根据uid更新缓存
     * @param $uid
     */
    protected function cacheUser($uid)
    {
        if ($uid) {
            UserUtil::CacheUser($uid, true);
        }
    }

    /**
     * 更新用户信息
     * @param $msgData
     */
    protected function updateUserByMsgData($msgData)
    {
        $params = array();
        if (isset($msgData['Name'])) {
            $params['username'] = $msgData['Name'];
            $params['realname'] = $msgData['Name'];
        }
        if (isset($msgData['Gender'])) {
            $params['gender'] = ($msgData['Gender'] == 1) ? 1 : 0;
        }
        if (isset($msgData['Mobile'])) {
            $params['mobile'] = $msgData['Mobile'];
        }
        if (isset($msgData['Email'])) {
            $params['email'] = $msgData['Email'];
        }
        if (isset($msgData['Status'])) {
            $params['status'] = ($msgData['Status'] == 2) ? 2 : 0;
        }
        if (isset($msgData['Position'])) {
            $params['positionid'] = $this->addPositionForName($msgData['Position']);
        }
        if (isset($msgData['Department'])) {
            $params['deptid'] = $this->getDeptid($msgData['Department']);
        }
        $uid = UserBinding::model()->fetchUidByValue($msgData['UserID']);
        if (!empty($params)) {
            User::model()->updateAll($params, 'uid = :uid', array(':uid' => $uid));
        }
        if (isset($msgData['Avatar'])) {
            $userProfile = UserProfile::model()->fetch("uid = :uid", array(':uid' => $uid));
            if (empty($userProfile)) {
                UserProfile::model()->add(array(
                    'uid' => $uid,
                    'avatar_small' => $msgData['Avatar']
                ));
            } else {
                UserProfile::model()->updateAll(array('avatar_small' => $msgData['Avatar']), 'uid = :uid', array(':uid' => $uid));
            }
        }
        if (isset($msgData['NewUserID'])) {
            UserBinding::model()->updateAll(array(
                'bindvalue' => $msgData['NewUserID']
            ), 'uid = :uid AND app = :app', array(':uid' => $uid, ':app' => 'wxqy'));
        }
        return $uid;
    }

    /**
     * 创建用户
     * @param $msgData
     */
    protected function createUserByMsgData($msgData)
    {
        $salt = StringUtil::random(6);
        $password = '123456';
        $params = array(
            'username' => $msgData['Name'],
            'realname' => $msgData['Name'],
            'mobile' => isset($msgData['Mobile']) ? $msgData['Mobile'] : '',
            'email' => isset($msgData['Email']) ? $msgData['Email'] : '',
            'gender' => ($msgData['Gender'] == 1) ? 1 : 0,
            'deptid' => $this->getDeptid($msgData['Department']),
            'roleid' => 3,//从企业微信同步过来的用户，默认的角色给普通成员
            'positionid' => isset($msgData['Position']) ? $this->addPositionForName($msgData['Position']) : 0,
            'salt' => $salt,
            'password' => md5(md5($password) . $salt),
            'createtime' => time(),
            'guid' => StringUtil::createGuid(),
        );
        $binding = UserBinding::model()->fetch('bindvalue=:bindvalue AND app=:app', array(':bindvalue' => $msgData['UserID'], ':app' => 'wxqy'));
        //有手机号码根据手机号码查询
        if (empty($msgData['Mobile'])) {
            $user = User::model()->fetch('username = :username', array(':username' => $msgData['Name']));
        } else {
            //or用户名查询是 避免这个用户可能存在2个手机号
            $user = User::model()->fetch('mobile = :mobile or username = :username', array(':mobile' => $msgData['mobile'], ':username' => $msgData['Name']));
        }
        if (!empty($user)) {
            $uid = $user['uid'];
        }
        if (empty($binding) && empty($user)) {
            $uid = User::model()->add($params, true);
            $addProfile = array(
                'uid' => $uid,
                'telephone' => empty($msgData['Telephone']) ? '' : $msgData['Telephone'],
                'avatar_middle' => empty($msgData['Avatar']) ? '' : $msgData['Avatar'],
            );
            UserProfile::model()->add($addProfile);
            // 添加辅助部门
            if (!empty($deptA)) {
                DepartmentRelated::model()->addBySyncWx($uid, $deptA);
            }
            Ibos::app()->db->createCommand()
                ->insert('{{user_count}}', array('uid' => $uid));
            $ip = Ibos::app()->request->userHostAddress;
            Ibos::app()->db->createCommand()
                ->insert('{{user_status}}', array('uid' => $uid, 'regip' => $ip, 'lastip' => $ip));
            UserBinding::model()->addUserBinding(array(
                'uid' => $uid,
                'bindvalue' => $msgData['UserID'],
                'app' => 'wxqy'
            ));
        } elseif (empty($binding) && !empty($user)) {
            //有用户，没绑。添加绑定
            UserBinding::model()->deleteAll("uid = {$user['uid']} AND app = 'wxqy'");
            UserBinding::model()->addUserBinding(array(
                'uid' => $user['uid'],
                'bindvalue' => $msgData['UserID'],
                'app' => 'wxqy'
            ));
        }
        if (isset($uid)) {
            return $uid;
        } else {
            return '';
        }
    }

    /**
     * 创建部门
     * @param $msgData
     */
    protected function createPartyByMsgData($msgData)
    {
        if ($msgData['ParentId'] == 1) {
            $pid = 0;
        } else {
            $deptid = DepartmentBinding::model()->getDeptidByBindingValueAndApp($msgData['ParentId']);
            if (empty($deptid)) {
                $pid = 0;
            } else {
                $pid = $deptid;
            }
        }
        $binding = DepartmentBinding::model()->getDeptidByBindingValueAndApp($msgData['Id']);
        $dept = Department::model()->fetch('deptname = :deptname', array(':deptname' => $msgData['Name']));
        if (empty($binding) && empty($dept)) {
            $params = array(
                'deptname' => $msgData['Name'],
                'pid' => $pid,
                'sort' => $msgData['Order'],
            );
            $deptid = Department::model()->add($params, true);
            DepartmentBinding::model()->add(array(
                'deptid' => $deptid,
                'bindvalue' => $msgData['Id'],
                'app' => 'wxqy'
            ));
        }
    }

    /**
     * 更新部门
     * @param $msgData
     */
    protected function updatePartyByMsgData($msgData)
    {
        $parmas = array();
        if (isset($msgData['Name'])) {
            $parmas['deptname'] = $msgData['Name'];
        }
        if (isset($msgData['ParentId'])) {
            $deptid = DepartmentBinding::model()->getDeptidByBindingValueAndApp($msgData['ParentId']);
            $parmas['pid'] = $deptid;
        }
        if (!empty($parmas)) {
            $deptid = DepartmentBinding::model()->getDeptidByBindingValueAndApp($msgData['Id']);
            Department::model()->updateAll($parmas, "deptid = :deptid", array(':deptid' => $deptid));
        }
    }

    /**
     * 删除IBOS部门
     * @param $id
     */
    protected function deletePartyById($id)
    {
        $deptid = DepartmentBinding::model()->getDeptidByBindingValueAndApp($id);
        if (!empty($deptid)) {
            $subDeptids = Department::model()->fetchDeptAllChildOfChildByDeptID($deptid);
            $delDeptids = array_unique(array_merge($subDeptids, array($deptid)));
            $delDeptidsStr = ArrayUtil::formatArrayForSearchInByArray($delDeptids);
            Department::model()->deleteAll("deptid IN ({$delDeptidsStr})");
            DepartmentBinding::model()->deleteAll("deptid IN ({$delDeptidsStr}) AND app = 'wxqy'");
            $uids = Ibos::app()->db->createCommand()
                ->select('uid')
                ->from(User::model()->tableName())
                ->where("deptid IN ({$delDeptidsStr})")
                ->queryColumn();
            if (!empty($uids)) {
                $uidsStr = ArrayUtil::formatArrayForSearchInByArray($uids);
                User::model()->updateAll(array('status' => 2, 'deptid' => 0), "uid IN ({$uidsStr})");
                UserBinding::model()->deleteAll("uid IN ({$uidsStr}) AND app = 'wxqy'");
            }
        }
    }

    /**
     * 同步日志记录
     * @param $message
     * @param $type 同步模式 1 主动(web界面点击同步) 2被动 (企业微信回调)
     */
    public function syncWxLog(array $message, $type)
    {
        switch ($type) {
            case 1:
                $level = 'activeSyncWx';
                break;
            case 2:
                $level = 'passiveSyncWx';
                break;
        }
        $timeStamp = time();
        $message['time'] = date("Y-m-d H:i:s", $timeStamp);
        Log::writeLogTable($message, 'system', $level);
    }
}
