<?php

/**
 * 组织架构模块用户控制器文件
 *
 * @author banyanCheung <banyan@ibos.com.cn>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2012-2013 IBOS Inc
 */
/**
 * 组织架构模块用户控制器类
 *
 * @package application.modules.dashboard.controllers
 * @author banyanCheung <banyan@ibos.com.cn>
 * @version $Id: UserController.php 4321 2014-10-09 07:42:26Z gzpjh $
 */

namespace application\modules\dashboard\controllers;

use application\core\utils\ArrayUtil;
use application\core\utils\Attach;
use application\core\utils\Cache as CacheUtil;
use application\core\utils\Convert;
use application\core\utils\Env;
use application\core\utils\File;
use application\core\utils\Ibos;
use application\core\utils\Org;
use application\core\utils\OrgIO;
use application\core\utils\PHPExcel;
use application\core\utils\StringUtil;
use application\core\utils\WebSite;
use application\modules\dashboard\model\Cache;
use application\modules\dashboard\utils\SyncWx;
use application\modules\department\components\DepartmentCategory as ICDepartmentCategory;
use application\modules\department\model\Department;
use application\modules\department\model\DepartmentRelated;
use application\modules\main\components\CommonAttach;
use application\modules\main\model\Setting;
use application\modules\main\utils\Main;
use application\modules\position\model\Position;
use application\modules\position\model\PositionRelated;
use application\modules\role\model\Role;
use application\modules\role\model\RoleRelated;
use application\modules\user\model\User;
use application\modules\user\model\UserCount;
use application\modules\user\model\UserProfile;
use application\modules\user\model\UserStatus;
use application\modules\user\utils\User as UserUtil;
use CHtml;

class UserController extends OrganizationbaseController
{

    const IMPORT_TPL = '/data/tpl/user_import.xls';

    /*
     * 员工上下级排列数据
     */

    static public $userList = array();

    /**
     *
     * @var string 下拉列表中的<option>格式字符串
     */
    public $selectFormat = "<option value='\$deptid' \$selected>\$spacer\$deptname</option>";

    /**
     * 用户条数
     * @var
     */
    protected $userCount;

    /**
     * 用户状态('enabled', 'lock', 'disabled', 'all')
     * @var
     */
    protected $type;
    /**
     * 浏览操作
     * @return void
     */
    public function actionIndex()
    {
        $data['unit'] = Ibos::app()->setting->get('setting/unit');
        $data['unit']['fullname'] = isset($data['unit']['fullname']) ? $data['unit']['fullname'] : '';
        // 获取分支部门的deptid
        $deptList = Department::model()->fetchAll('isbranch = 1');
        $deptArr = Convert::getSubByKey($deptList, 'deptid');
        $data['deptStr'] = implode(',', $deptArr);
        $auth = SyncWx::getInstance()->checkBindingWxAndAuthContact();
        if ($auth['isBindingWx'] == false){
            $data['canwrite'] = 1;
        }else{
            if ($auth['isBindingWx'] && $auth['isContacntAuth']){
                $data['canwrite'] = 1;
            }else{
                $data['canwrite'] = 0;
            }
        }
        $data['contacturl'] = $this->getContactSuiteUrl();
        $this->render('index', $data);
    }

    /**
     * 得到通讯录套件的url
     * @return string
     */
    protected function getContactSuiteUrl()
    {
        $aeskey = Setting::model()->fetchSettingValueByKey('aeskey');
        $url = 'Api/WxCorp/isBinding';
        $res = WebSite::getInstance()->fetch($url, array('aeskey' => $aeskey));
        if (!is_array($res)){
            $result = \CJSON::decode($res, true);
            switch ($result['type']){
                case 1 :
                    $unit = Ibos::app()->setting->get('setting/unit');
                    $aeskey = Ibos::app()->setting->get('setting/aeskey');
                    $contactUrl = WebSite::getInstance()->build('Wxapi/Api/toWx', array(
                        'state' => base64_encode(json_encode(array(
                            'domain' => $unit['systemurl'],
                            'uid' => $result['uid'],
                            'ibosuid' => Ibos::app()->user->uid,
                            'aeskey' => $aeskey,
                            'version' => strtolower(implode(',', array(
                                ENGINE,
                                VERSION,
                                VERSION_TYPE
                            )))
                        ))),
                        'id' => 'tjdb492d2f4449b5d0'
                    ));
                    $returnUrl =  $contactUrl;
                    break;
                case 2:
                    $returnUrl =  Ibos::app()->urlManager->createUrl('dashboard/wxsync/app');
                    break;
                case 3:
                    $returnUrl =  Ibos::app()->urlManager->createUrl('dashboard/wxsync/app');
                    break;
                default:
                    $returnUrl =  Ibos::app()->urlManager->createUrl('dashboard/wxsync/app');
                    break;
            }
        }else{
            $returnUrl =  Ibos::app()->urlManager->createUrl('dashboard/wxsync/app');
        }
        return $returnUrl;
    }

    /**
     * 获取 index 页面用户列表数据方法
     * @return json
     */
    public function actionGetUserList()
    {
        $draw = Env::getRequest('draw');
        $this->type = Env::getRequest('type');
        if (!in_array($this->type, array('enabled', 'lock', 'disabled', 'all'))) {
            $this->type = 'enabled';
        }
        $this->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '调用成功',
            'data' => $this->handleUserListDataByCondition(),
            'draw' => $draw,
            'recordsFiltered' => $this->getUserNumberByConditions(),
        ));
    }

    /**
     * 处理返回用户列表数据(获取用户数据，处理部门、岗位等等名称)
     * @param $type
     * @return array
     */
    private function handleUserListDataByCondition()
    {
        $userData = $this->getUserDataByCondition();
        $userList = array_map(function ($user) {
            return array(
                'uid' => $user['uid'],
                'realname' => $user['realname'],
                'deptname' => Department::model()->fetchDeptNameByDeptId($user['deptid']),
                'posname' => Position::model()->fetchPosNameByPosId($user['positionid']),
                'rolename' => Role::model()->getRoleNameByRoleid($user['roleid']),
                'mobile' => $user['mobile'],
                'weixin' => $user['weixin'],
                'status' => $user['status'],
                'avatar_small' => Org::getDataStatic($user['uid'], 'avatar', 'small'),
            );
        }, $userData);
        return $this->addRelatedRole($userList);
    }

    /**
     * 根据参数条件获取用户数据
     * @return mixed
     */
    protected function getUserDataByCondition()
    {
        $offset = intval(Env::getRequest('start'));
        $limit = intval(Env::getRequest('length'));
        $query = $this->getUserObjByConditions();
        return  $query->limit($limit, $offset)->queryAll();
    }

    /**
     * 获取用户数量(用户分页)
     * @return bool|\CDbDataReader|mixed|string
     */
    protected function getUserNumberByConditions()
    {
        $quesry = $this->getUserObjByConditions(false);
        return $this->userCount = $quesry->queryScalar();
    }
    /**
     * 根据参数返回dbConnection对象
     * @param $type
     * @return $this|\CDbCommand|static
     */
    protected function getUserObjByConditions($count = true)
    {
        if ($count){
            $select = 'u.*';
        }else{
            $select = 'count(distinct(u.uid))';
        }
        $status = $this->getUserStatusByType();
        $deptid = Env::getRequest('deptid');
        $deptid = $deptid?intval($deptid):null;
        $search = Env::getRequest('search');
        $query = User::model()->dbConnection->createCommand()
            ->select($select)
            ->from(User::model()->tableName() . ' AS u')
            ->leftJoin(DepartmentRelated::model()->tableName() . ' AS dr', 'u.uid=dr.uid');
        if ($status != null){
            $query = $query->where("u.status = :status",array(':status' =>$status));
        }
        if ($deptid !==null) {
            $query = $query->andWhere("u.deptid = :udeptid OR dr.deptid = :ddeptid ",array(':udeptid'=>$deptid,':ddeptid' =>$deptid));
        }
        if (!empty($search['value'])){
            $key = '%'.CHtml::encode($search['value']).'%';
            $query = $query->andWhere("u.username LIKE :key1 OR u.realname LIKE :key2 OR u.mobile LIKE :key3",
                array('key1'=>$key,'key2'=>$key,'key3'=>$key));
        }
        if ($count){
            $query = $query->group("u.uid");
        }
        return $query;
    }

    /**
     *通过type获取用户状态数值(数据库对于的状态数值)
     * @return null|string
     */
    protected function getUserStatusByType()
    {
        switch ($this->type) {
            case 'enabled':
                $status = '0';
                break;
            case 'lock':
                $status = '1';
                break;
            case 'disabled' :
                $status = '2';
                break;
            default:
                $status  = null;
                break;
        }
        return $status;
    }

    /**
     * 获取部门树
     * @return json
     */
    public function actionGetDeptTree()
    {
        $this->getDeptTree();
    }

    /**
     * 新增操作
     * @return void
     */
    public function actionAdd()
    {
        if (Env::submitCheck('userSubmit')) {
            //暂且这样获取，后期细化到action 里面 写方法获取参数
            $userData = $_POST;
            $this->checkUserDataParamsLength($userData);
            $origPass = isset($userData['password'])?$userData['password']:'';
            $userData = UserUtil::handleUpdateParams($userData,true);
            $userData['createtime'] = TIMESTAMP;
            $newId = UserUtil::createUserByUserData($userData);
            if ($newId){
                // 上传头像
                if( !empty($_FILES['avatar']['type']) && !empty($_FILES['avatar']['name']) ){
                    if(!$this->uploadAvatar($newId)) {
                        $this->error(Ibos::lang('Upload avatar failure'));
                    }
                }
                // 重建缓存，给新加的用户生成缓存
                $status = isset($userData['status'])?$userData['status']:'';
                UserUtil::rebuildUserCache($newId,$status,$origPass);
                SyncWx::getInstance()->addWxUser($newId);
                $this->success(Ibos::lang('Save succeed', 'message'), $this->createUrl('user/index'));
            }else {
                $this->error(Ibos::lang('Add user failed'), $this->createUrl('user/index'));
            }
        } else {
            $deptid = "";
            $manager = "";
            $account = Ibos::app()->setting->get('setting/account');
            if ($account['mixed']) {
                $preg = "[0-9]+[A-Za-z]+|[A-Za-z]+[0-9]+";
            } else {
                $preg = "^[A-Za-z0-9\!\@\#\$\%\^\&\*\.\~]{" . $account['minlength'] . ",32}$";
            }
            if ($deptid = Env::getRequest('deptid')) {
                $deptid = StringUtil::wrapId(Env::getRequest('deptid'), 'd');
                $manager = StringUtil::wrapId(Department::model()->fetchManagerByDeptid(Env::getRequest('deptid')), 'u');
            }
            $this->render('add', array(
                'deptid' => $deptid,
                'manager' => $manager,
                'passwordLength' => $account['minlength'],
                'preg' => $preg,
                'roles' => Role::model()->fetchAll(),
                'lang' => Ibos::getLangSources(),
                'assetUrl' => $this->getAssetUrl(),
            ));
        }
    }

    /**
     *
     */
    public function actionGetavailable()
    {
        $limit = LICENCE_LIMIT;
        $uidArray = User::model()->fetchUidA(false);
        $count = count($uidArray);
        $remain = $limit - $count;
        $this->ajaxReturn(
            array(
                'isSuccess' => true,
                'current' => $count,
                'remain' => $remain
            ));
    }

    /**
     * 编辑操作
     * @return void
     */
    public function actionEdit()
    {
        $op = Env::getRequest('op');
        if ($op && in_array($op, array('enabled', 'disabled', 'lock'))) {
            $ids = Env::getRequest('uid');
            return $this->setStatus($op, $ids);
        }
        $uid = Env::getRequest('uid');
        $user = User::model()->fetchByUid($uid);
        if (empty($user)) {
            return;
        }
        $oldstatus =  $user['status'];
        $positionid = $user['positionid'];//拿到修改之前的positionid
        if (Env::submitCheck('userSubmit')) {
            // 上传头像
            if( !empty($_FILES['avatar']['type']) && !empty($_FILES['avatar']['name']) ){
                if(!$this->uploadAvatar($uid)) {
                    return $this->error(Ibos::lang('Upload avatar failure'));
                }
            }
            $requestData = $_POST;
            $this->checkUserDataParamsLength($requestData);
            $userData = UserUtil::handleUpdateParams($requestData);
            $data = User::model()->create($userData);
            User::model()->checkUnique($data);
            if ($data['status'] != User::USER_STATUS_NORMAL) {
                $canDisabled = User::model()->checkCanDisabled($uid);
                if (false === $canDisabled) {
                    return $this->error(Ibos::lang('make sure at least one admin'));
                }
            }
            // 为空不修改密码
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = md5(md5($data['password']) . $user['salt']);
                $data['lastchangepass'] = TIMESTAMP;
            }
            $status = isset($userData['status'])?$userData['status']:'';
            $isChangeStatus = $status != $oldstatus;
            if ($isChangeStatus) {
                if($status == 2) {
                    SyncWx::getInstance()->batchDeleteWxUserByUids($uid);
                    Org::hookSyncUser($uid, '', 0);
                } else {
                    SyncWx::getInstance()->addWxUser($uid);
                    Org::hookSyncUser($uid, '', 2);
                }
            }
            User::model()->updateByUid($uid, $data);
            //关联更新(辅助部门 辅助岗位 岗位 直属下属)
            UserUtil::userOtherAssociationUp($userData,$positionid);
            UserUtil::rebuildUserCache($uid,$status);
            if (!$isChangeStatus) {
            SyncWx::getInstance()->updateWxUser($uid);
            }
            $this->success(Ibos::lang('Save succeed', 'message'), $this->createUrl('user/index'));
        } else {
            if (empty($user)) {
                $this->error(Ibos::lang('Request param'), $this->createUrl('user/index'));
            }
            //需要重新去查找一下刚用户的最新信息，并且要强制生成新缓存，不然总是去拿旧的缓存，数据没有更新
            $user = User::model()->fetchByUid($uid, true, true);
            $user["auxiliarydept"] = DepartmentRelated::model()->fetchAllDeptIdByUid($user['uid']);
            $user["auxiliarypos"] = PositionRelated::model()->fetchAllPositionIdByUid($user['uid']);
            $user["auxiliaryrole"] = RoleRelated::model()->fetchAllRoleIdByUid($user['uid']);
            $user['subordinate'] = User::model()->fetchSubUidByUid($user['uid']); // 获取所有直属下属uid
            $account = Ibos::app()->setting->get('setting/account');
            if ($account['mixed']) {
                $preg = "[0-9]+[A-Za-z]+|[A-Za-z]+[0-9]+";
            } else {
                $preg = "^[A-Za-z0-9\!\@\#\$\%\^\&\*\.\~]{" . $account['minlength'] . ",32}$";
            }
            $param = array(
                'user' => $user,
                'passwordLength' => $account['minlength'],
                'preg' => $preg,
                'roles' => Role::model()->fetchAll()
            );
            $this->render('edit', $param);
        }
    }

    /**
     * 导出操作
     * @return void
     */
    public function actionExport()
    {
        $uid = urldecode(Env::getRequest('uid'));
        return UserUtil::exportUser(explode(',', trim($uid, ',')));
    }

    /**
     * 导入用户一系列操作入口
     */
    public function actionImport()
    {
        $op = Env::getRequest('op');
        if (in_array($op, array('downloadTpl', 'import', 'downError'))) {
            $this->$op();
        }
    }

    /**
     * 用户上下级关系
     */
    public function actionRelation()
    {
        $users = User::model()->findUserIndexByUid();
        $position = array();
        foreach ($users as $user) {
            $position[$user['uid']] = $user['positionid'];
        }
        $PositionArray = Position::model()->findPositionNameIndexByPositionid(array_unique(array_values($position)));
        $upUsers = array(); // 最顶级人员(没上司的人)
        foreach ($users as $user) {
            $subordinate = User::model()->fetchSubUidByUid($user['uid']);
            if ($user['upuid'] == 0 && empty($subordinate)) {
                $upUsers[] = array(
                    'uid' => $user['uid'],
                    'name' => $user['realname'],
                    'position' => !empty($user['position']) ? $PositionArray[$user['position']] : '',
                );
            }
        }
        $param = array(
            'upUsers' => $upUsers,
            'assetUrl' => Ibos::app()->assetManager->getAssetsUrl('dashboard')
        );
        $op = Env::getRequest('op');
        if (in_array($op, array('getUsers', 'setUpuid'))) {
            $this->$op();
        } else {
            $alias = "application.modules.dashboard.views.user.relation";
            $html = $this->renderPartial($alias, $param, true);
            $this->ajaxReturn(array('isSuccess' => true, 'html' => $html));
        }
    }

    /**
     * 获取上下级关系用户数据
     */
    protected function getUsers()
    {
        $users = User::model()->findUserIndexByUid();
        $res = array();
        foreach ($users as $user) {
            $subordinate = User::model()->fetchSubUidByUid($user['uid']);
            if ($user['upuid'] != 0 || !empty($subordinate)) {
                $res[] = array(
                    'id' => $user['uid'],
                    'uid' => $user['uid'],
                    'name' => $user['realname'],
                    'pid' => $user['upuid'],
                    'pId' => $user['upuid']
                );
            }
        }
        $this->ajaxReturn($res);
    }

    /**
     * 移动上下级关系
     */
    protected function setUpuid()
    {
        $uid = Env::getRequest('id');
        $upuid = Env::getRequest('pid');
        if (!empty($uid)) {
            User::model()->modify($uid, array('upuid' => $upuid));
            Org::update();
            CacheUtil::update();
        }
        $this->ajaxReturn(array('isSuccess' => true));
    }

    /**
     * 下载模板文件
     */
    protected function downloadTpl()
    {
        $file = PATH_ROOT . self::IMPORT_TPL;
        $fileName = iconv('utf-8', 'gbk', '用户导入数据.' . pathinfo($file, PATHINFO_EXTENSION));
        if (is_file($file)) {
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=" . $fileName);
            readfile($file);
            exit;
        } else {
            $this->error("抱歉，找不到模板文件！");
        }
    }

    /**
     * 导入操作
     */
//  protected function import() {
//      Cache::model()->deleteAll( "`cachekey` = 'userimportfail'" );
//      set_time_limit( 0 ); //避免php脚本超时
//      $attachId = intval( Env::getRequest( 'aid' ) );
//      $attachs = Attach::getAttachData( $attachId, false );
//      $attach = array_shift( $attachs ); // 附件
//      $file = File::getAttachUrl() . '/' . $attach['attachment'];
//      $reader = new Spreadsheet_Excel_Reader();
//      $reader->setOutputEncoding( 'utf-8' );
//      $reader->read( $file );
//      $err = array();
//      $successCount = 0;
//      if ( isset( $reader->sheets[0]['cells'] ) && is_array( $reader->sheets[0]['cells'] ) ) {
//          unset( $reader->sheets[0]['cells'][1] ); // 去掉excel头
//          $count = count( $reader->sheets[0]['cells'] );
//          $users = UserUtil::loadUser();
//          $allUsers = User::model()->fetchAllSortByPk( 'uid' ); // 全部用户，包括锁定、禁用等
//          $convert = array();
//          foreach ( $allUsers as $user ) {
//              $convert['username'][] = $user['username']; // 已存在的用户名
//              $convert['mobile'][] = $user['mobile']; // 已存在的手机号
//              $convert['email'][] = $user['email']; // 已存在的邮箱
//              $convert['jobnumber'][] = $user['jobnumber']; // 已存在的工号
//          }
//          // 邮件格式
//          $emailPreg = "/^[_.0-9a-z-a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,4}$/";
//          $ip = Ibos::app()->setting->get( 'clientip' );
//          foreach ( $reader->sheets[0]['cells'] as $k => $row ) {
//              //以下数组下标跟导入表的每一列位置对应，如导入出现问题，请检查位置与格式！
//              $salt = StringUtil::random( 6 );
//              $origPass = isset( $row[2] ) ? $row[2] : '';
//              $data = array(
//                  'salt' => $salt,
//                  'username' => isset( $row[1] ) ? trim( $row[1] ) : '', // 姓名
//                  'password' => !empty( $origPass ) ? md5( md5( trim( $origPass ) ) . $salt ) : '', // 密码
//                  'realname' => isset( $row[3] ) ? trim( $row[3] ) : '', // 真实姓名
//                  'gender' => isset( $row[4] ) && trim( $row[4] ) == '女' ? 0 : 1, // 性别
//                  'mobile' => isset( $row[5] ) ? trim( $row[5] ) : '', // 手机
//                  'email' => isset( $row[6] ) ? trim( $row[6] ) : '', // 邮箱
//                  'weixin' => isset( $row[7] ) ? trim( $row[7] ) : '', // 微信
//                  'jobnumber' => isset( $row[8] ) ? trim( $row[8] ) : '', // 工号
//              );
//              if ( empty( $data['username'] ) || empty( $data['password'] ) || empty( $data['realname'] ) || empty( $data['mobile'] ) || empty( $data['email'] ) ) {
//                  $err[$k] = array( 'reason' => '用户名、密码、真实姓名、手机、邮箱不能为空！' );
//              } else if ( in_array( $data['username'], $convert['username'] ) ) {
//                  $err[$k] = array( 'reason' => '用户名已存在！' );
//              } else if ( in_array( $data['mobile'], $convert['mobile'] ) ) {
//                  $err[$k] = array( 'reason' => '手机号码已存在！' );
//              } else if ( in_array( $data['email'], $convert['email'] ) ) {
//                  $err[$k] = array( 'reason' => '邮箱已存在！' );
//              } else if ( !empty( $data['jobnumber'] ) && in_array( $data['jobnumber'], $convert['jobnumber'] ) ) {
//                  $err[$k] = array( 'reason' => '工号已存在！' );
//              } else if ( !preg_match( $emailPreg, $data['email'] ) ) {
//                  $err[$k] = array( 'reason' => '邮件格式错误！' );
//              }
//              if ( isset( $err[$k]['reason'] ) ) {
//                  $err[$k]['username'] = $data['username'];
//                  $err[$k]['realname'] = $data['realname'];
//              } else {
//                  $newId = User::model()->add( $data, true );
//                  UserCount::model()->add( array( 'uid' => $newId ) );
//                  UserStatus::model()->add(
//                          array(
//                              'uid' => $newId,
//                              'regip' => $ip,
//                              'lastip' => $ip
//                          )
//                  );
//                  UserProfile::model()->add( array( 'uid' => $newId ) );
//                  $newUser = User::model()->fetchByPk( $newId );
//                  $users[$newId] = UserUtil::wrapUserInfo( $newUser );
//                  // 同步用户钩子
//                  Org::hookSyncUser( $newId, $origPass, 1 );
//                  $successCount++;
//              }
//          }
//          if ( $successCount > 0 ) {
//              User::model()->makeCache( $users );
//              // 更新组织架构js调用接口
//              Org::update();
//              CacheUtil::update();
//          }
//          if ( !empty( $err ) ) {
//              Cache::model()->add( array( 'cachekey' => 'userimportfail', 'cachevalue' => serialize( $err ) ) );
//          }
//          @unlink( $file ); // 删除文件
//          $this->ajaxReturn( array( 'isSuccess' => true, 'successCount' => $successCount, 'errorCount' => count( $err ), 'url' => $this->createUrl( 'user/import', array( 'op' => 'downError' ) ) ) );
//      } else {
//          $this->ajaxReturn( array( 'isSuccess' => true, 'successCount' => 0, 'errorCount' => 0, 'url' => '' ) );
//      }
//  }

    protected function import()
    {
        $attachId = intval(Env::getRequest('aid'));
        $attachs = Attach::getAttachData($attachId, false);
        $attach = array_shift($attachs); // 附件
        $file = File::getAttachUrl() . '/' . $attach['attachment'];
        $data = PHPExcel::excelToArray($file, array(0, 1, 2));
        $config = array(
            'department' => 0,
            'mobile' => 1,
            'password' => 2,
            'realname' => 3,
            'gender' => 4,
            'email' => 5,
            'wechat' => 6,
            'jobnumer' => 7,
            'username' => 8,
            'birthday' => 9,
            'telephone' => 10,
            'address' => 11,
            'qq' => 12,
            'bio' => 13,
        );
        $ajaxReturn = OrgIO::import($data, $config);
        @unlink($file); // 删除文件
        Org::update();
        CacheUtil::update();
        $this->ajaxReturn($ajaxReturn);
    }

    /**
     * 下载导入错误文件
     * 导出CSV格式
     */
//  protected function downError() {
//      $error = Cache::model()->fetchArrayByPk( 'userimportfail' );
//      Cache::model()->delete( "`cachekey` = 'userimportfail'" );
//      $fieldArr = array(
//          Ibos::lang( 'Line' ),
//          Ibos::lang( 'Username' ),
//          Ibos::lang( 'Realname' ),
//          Ibos::lang( 'Error reason' ),
//      );
//      $str = implode( ',', $fieldArr ) . "\n";
//      foreach ( $error as $line => $row ) {
//          $param = array( $line, $row['username'], $row['realname'], $row['reason'] );
//          $str .= implode( ',', $param ) . "\n"; //用引文逗号分开
//      }
//      $outputStr = iconv( 'utf-8', 'gbk//ignore', $str );
//      $filename = Ibos::lang( 'Import error record' ) . '.csv';
//      File::exportCsv( $filename, $outputStr );
//  }
    /**
     * 下载导入用户错误文件
     * 导出Excel格式
     */
    protected function downError()
    {
        $error = Cache::model()->fetchArrayByPk('userimportfail');
        Cache::model()->delete("`cachekey` = 'userimportfail'");
        $return = array();
        foreach ($error as $key => $row) {
            $return[$key]['line'] = $key;
            $return[$key]['username'] = $row['username'];
            $return[$key]['realname'] = $row['realname'];
            $return[$key]['reason'] = $row['reason'];
        }
        $filename = $filename = date('Y-m-d') . '用户导入错误信息.xls';
        $fieldArr = array(
            Ibos::lang('Line'),
            Ibos::lang('Username'),
            Ibos::lang('Realname'),
            Ibos::lang('Error reason'),
        );
        PHPExcel::exportToExcel($filename, $fieldArr, $return);
    }

    /**
     * 编辑动作: 设置用户状态
     * @param string $status 状态标识
     * @param string $uids 用户id
     * @return void
     */
    protected function setStatus($status, $uids)
    {
        $uidArr = explode(',', trim($uids, ','));
        $attributes = array();
        switch ($status) {
            case 'lock':
                $attributes['status'] = 1;
                break;
            case 'disabled':
                $attributes['status'] = 2;
                SyncWx::getInstance()->batchDeleteWxUserByUids($uidArr);
                Org::hookSyncUser($uids, '', 0);
                break;
            case 'enabled':
            default:
                $attributes['status'] = 0;
                foreach ($uidArr as $uid){
                    SyncWx::getInstance()->addWxUser($uid);
                }
                Org::hookSyncUser($uids, '', 2);
                break;
        }

        $canDisabled = User::model()->checkCanDisabled($uidArr);
        if (false === $canDisabled) {
            return $this->ajaxReturn(array(
                'isSuccess' => false,
                'msg' => Ibos::lang('make sure at least one admin')
            ));
        }
        $return = User::model()->updateByUids($uidArr, $attributes);
        UserUtil::wrapUserInfo($uidArr, false, true);
        Org::update();

        // 更新 position 的缓存信息
        CacheUtil::update(array('position'));
        CacheUtil::load(array('position'));

        return $this->ajaxReturn(array('isSuccess' => !!$return), 'json');
    }

    /**
     * 获取左侧分类树
     */
    protected function getDeptTree()
    {
        $component = new ICDepartmentCategory('application\modules\department\model\Department', '', array('index' => 'deptid', 'name' => 'deptname'));
        $this->ajaxReturn($component->getAjaxCategory($component->getData()), 'json');
    }

    /**
     * 用formValidator异步检查数据是否已被注册
     */
    public function actionIsRegistered()
    {
        //$fieldName获取要检查的字段名
        $fieldName = Env::getRequest('clientid');
        //$fieldValue获取此字段用户输入的值
        $fieldValue = Env::getRequest($fieldName);
        //如果有传递uid，是用户编辑资料，没有uid，是新注册资料
        $uid = Env::getRequest('uid');
        if ($uid) {
            $userInfo = User::model()->findByPk($uid);
            $fieldExists = User::model()->fetch("$fieldName = '{$fieldValue}' and $fieldName != '{$userInfo[$fieldName]}'");
        } else {
            if ($fieldValue == '' || $fieldValue == null) {
                //若用户输入为空，则判断通过
                return $this->ajaxReturn(array('isSuccess' => true), 'json');
            } else {
                //查找数据库的$fieldName字段是否有$fieldValue这个值
                $fieldExists = User::model()->find("$fieldName = :getValue", array(":getValue" => $fieldValue));
            }
        }
        //有数据则表示已经注册，返回true，没数据表示没注册，返回false
        $isRegistered = $fieldExists ? true : false;
        return $this->ajaxReturn(array('isSuccess' => !$isRegistered), 'json');
    }

    /**
     * 添加辅助角色信息
     * @param array $list 数据列表
     * @return array
     */
    protected function addRelatedRole($list)
    {
        if (empty($list)) {
            return array();
        }
        $relatedRole = array();
        $uids = Convert::getSubByKey($list, 'uid');
        foreach ($uids as $uid) {
            $relatedRole[$uid] = array_map(function ($rid) {
                return Role::model()->getRoleNameByRoleid($rid);
            }, RoleRelated::model()->fetchAllRoleIdByUid($uid));
        }
        foreach ($list as $key => $value) {
            $list[$key]['relatedRole'] = $relatedRole[$value['uid']];
        }
        return $list;
    }

    /**
     * 后台上传头像
     */
    private function uploadAvatar($uid)
    {
        // 处理后缀名
        $fileName = $_FILES['avatar']['name'];
        $fileExt = StringUtil::getFileExt($fileName);
        // 检查文件后缀
        if (!in_array($fileExt, array('jpg', 'gif', 'png'))) {
           return false;
        }
        // 判断上传文件大小
        $fileSize = $_FILES["avatar"]["size"] / (1024 * 1024);
        if($fileSize > 2) {
            // 文件大小需要小于2兆
            return false;
        }

        // 获取上传域并上传到临时目录
        $upload = new CommonAttach('avatar');
        $upload->upload();
        if (!$upload->getIsUpoad()) {
            return false;
        } else {
            $info = $upload->getUpload()->getAttach();
            $file = File::getAttachUrl() . '/' . $info['type'] . '/' . $info['attachment'];
            $fileUrl = File::imageName($file);
            $tempSize = File::imageSize($fileUrl);
            //判断宽和高是否符合头像要求
            if ($tempSize[0] < 180 || $tempSize[1] < 180) {
                return false;
            }
            $this->setAvatar($file, $uid);
        }
        return true;
    }

    private function setAvatar($src, $uid)
    {
        set_time_limit(120);
        //图片裁剪数据
        $avatarArray = Ibos::engine()->io()->file()->createAvatar($src, array('x'=>0,'y'=>0,'w'=>'180','h'=>'180','uid' => $uid));
        UserProfile::model()->updateAll($avatarArray, "uid = {$uid}");
        UserUtil::wrapUserInfo($uid, true, true, true);
        Ibos::app()->user->setState('avatar_big', $avatarArray['avatar_big']);
        Ibos::app()->user->setState('avatar_middle', $avatarArray['avatar_middle']);
        Ibos::app()->user->setState('avatar_small', $avatarArray['avatar_small']);
        UserUtil::cleanCache($uid);
    }

    /**
     * 检测用户值的长度
     * @param $userData
     */
    protected function checkUserDataParamsLength($userData)
    {
        $err = UserUtil::checkRequrestUserDataLength($userData);
        if ($err){
            $this->error(Ibos::lang('Beyond the length of the','',array('{name}' => $err)));
        }
    }
}
