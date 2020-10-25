<?php
namespace app\common\api;

use app\common\logic\MupLogic;
use app\common\model\app\AppEmp;
use app\common\model\eba\Eba;
use app\common\model\eba\EbaAttr;
use app\common\model\mup\MupUser;
use app\common\model\mup\MupUserAttr;
use think\Db;

/**
 * Class WeiWork
 * @package app\common\api
 */
class WeiWork {
    /**
     * 根据微信通讯录中的账号找到对应的oit系统用户信息
     * @return array|string
     */
    public static function find_sys_user_info() {
        $user_info_weiwork = session('user_info_weiwork');
        $user_ext_info = MupUserAttr::get(['attr_val' => $user_info_weiwork['userid'], 'attr_id' => 'wx_user_id']);
        if (empty($user_ext_info)) {
            exit(lang("此微信账号在oit系统中没有绑定 操作用户"));
        }
        $user_info_system = MupUser::get($user_ext_info['user_id']);
        session('user_info_system', $user_info_system);
        session('user_id', $user_info_system['user_id']);
        session('emp_id', $user_info_system['emp_id']);
        return $user_info_system;
    }

    /**
     * 根据微信通讯录中的账号找到对应的oit客户
     * 根据微信通讯录中的账号查找有没有对应的oit客户
     * 一个企业微信账号,名下可能会有多个客户资料
     * 所以下单的时候，允许选择客户
     * @return null|static
     */
    public static function find_sys_eba_info() {
        $user_info_weiwork = session('user_info_weiwork');
        $eba_ext_info = EbaAttr::all(['attr_val' => $user_info_weiwork['userid'], 'attr_id' => 'wx_user_id'])->toArray();
        if (empty($eba_ext_info)) {
            exit(lang("登陆已失效，请重新登陆. 或 此微信账号在oit系统中没有绑定 客户,请管理员协助处理."));
        }
        // 可能会有多个客户
        $eba = new Eba();
        $where['eba_id'] = ['in', array_column($eba_ext_info, 'eba_id')];
        $eba_info_system = $eba->where($where)->select()->toArray();
        //session('eba_info_system', $eba_info_system);
        return $eba_info_system;
    }

    /**
     * 获取系统 业务员工
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function find_sys_app_emp_info() {
        $user_info_weiwork = session('user_info_weiwork');
        $where['other_im_no'] = $user_info_weiwork['userid'];
        $emp_info_system = Db::table('app_emp')->where($where)->find();
        if (empty($emp_info_system)) {
            exit(lang("登陆已失效，请重新登陆. 或 此微信账号在oit系统中没有绑定 员工,请管理员协助处理."));
        }
        return $emp_info_system;
    }

    /**
     * 获取系统 企业微信账号的员工资料
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function find_sys_emp_info() {
        $user_info_weiwork = session('user_info_weiwork');
        $where['other_im_no'] = $user_info_weiwork['userid'];
        $emp_info_system = Db::table('app_emp')->where($where)->find();
        if (empty($emp_info_system)) {
            exit(lang("登陆已失效，请重新登陆. 或 此微信账号在oit系统中没有绑定 员工,请管理员协助处理."));
        }
        return $emp_info_system;
    }
}
