<?php
namespace app\common\logic;

use app\common\model\eba\Eba;
use app\common\model\emp\Emp;
use app\common\model\mup\MupUser;
use app\common\model\mup\MupUserAttr;
use app\common\model\sup\Sup;
use think\Log;

/**
 * Class ManagerL
 * @package app\entrance\logic
 */
class MupLogic {
    /**
     *  检测登陆，分用户、员工、供应商、客户等
     * @return mixed
     */
    public static function check_login() {
        // 获取传输变量，判断登陆并转向正确模块，登记缓存
        $account = input('post.account');   //用户名|员工名
        $password = input('post.password'); //密码
        //$verify = input('post.verify'); //验证码

        if (empty($account)) {
            $error = lang('账号必须');
            return $error;
        }

        if (empty($password)) {
            $error = lang('密码必须');
            return $error;
        }

        // 查找登陆用户
        $user_info = self::get_login_user_pwd($account);
        if (empty($user_info)) {
            return lang('没有用户');
        }

        $user_password = $user_info['pwd'];
        if ($password != $user_password) {
            return lang('密码不正确');
        }

        return $user_info;
    }


    /**
     * @param $user_id
     * @return mixed
     * 返回第一条匹配到的用户登陆信息
     * 优先级别 0 用户 1 员工 2 客户 3 供应商
     * 注意user_id, emp_id, eba_id, sup_id 编号保证不一样
     */
    public static function get_login_user_pwd($user_id) {
        // 1 操作员
        $user = MupUser::get($user_id);
        if ($user != null) {
            $user_info = $user->getData();
            $data['user_type'] = 'user';
            $data['account_id'] = $user_info['user_id'];
            $data['account_name'] = $user_info['user_name'];
            // 使用 mup_user_attr 中的 email_pwd
            $mup_user_attr = new MupUserAttr();
            $where['user_id'] = $user_info['user_id'];
            $where['attr_id'] = 'web_pwd';
            $data['pwd'] = $mup_user_attr->where($where)->value('attr_val');
            //$data['pwd'] = $user_info['note_info'];
            $data['is_admin'] = $user_info['is_admin'];
            return $data;
        }

        // 2 员工
        $emp = Emp::get($user_id);
        if (!empty($emp)) {
            $emp_info = $emp->getData();
            $data['user_type'] = 'emp';
            $data['account_id'] = $emp_info['emp_id'];
            $data['account_name'] = $emp_info['name'];
            $data['pwd'] = $emp_info['timer_passwd'];
            $data['is_admin'] = 'N';
            return $data;
        }

        // 3 客户
        $eba = Eba::get($user_id);
        if (!empty($eba)) {
            $eba_info = $eba->getData();
            $data['user_type'] = 'eba';
            $data['account_id'] = $eba_info['eba_id'];
            $data['account_name'] = $eba_info['eba_name'];
            $data['pwd'] = $eba_info['pwd'];
            $data['is_admin'] = 'N';
            return $data;
        }

        // 4 供应商
        $sup = Sup::get($user_id);
        if (!empty($sup)) {
            $sup_info = $sup->getData();
            $data['user_type'] = 'sup';
            $data['account_id'] = $sup_info['sup_id'];
            $data['account_name'] = $sup_info['sup_name'];
            $data['pwd'] = $sup_info['other_im_no'];
            $data['is_admin'] = 'N';
            return $data;
        }

        return false;
    }

    /**
     * 返回数据库单字母权限与动作的对应关系
     * @return array
     */
    public static function system_actions() {
        return [
            'A' => 'index',                   //查看
            'B' => 'add,edit,remove',         //综合编辑
            'C' => 'check',                   //审核
            'D' => 'r_check',                 //反审
            'E' => 'execute',                 //执行
            'F' => 'print',                   //打印
            'G' => 'toout',                   //导出 export
            'H' => 'toin',                    //导入 import
            'I' => 'print_def',               //打印
            'U' => 'edit',                    //修改
            'V' => 'user_2',
            'W' => 'user_3',
            'P' => 'finish',                  // 完成
            'Q' => 'r_finish',                // 反完成
            'M' => 'acc_form_date',
            'N' => 'not_acc_from_date',
            'O' => 'not_acc_price',
            'J' => 'def_ext',
            'S' => 'add',                     //新增
            'T' => 'remove',                  //删除
        ];
    }

    /**
     * 返回动作的单字母
     * @param $action
     * @return bool|string
     */
    public static function action_to_char($action) {
        $char = false;
        switch ($action) {
            case 'index':
                $char = 'A';
                break;
            case 'add':
                $char = 'S';
                break;
            case 'edit':
                $char = 'U';
                break;
            case 'remove':
                $char = 'T';
                break;
            case 'check':
                $char = 'C';
                break;
            case 'r_check':
                $char = 'D';
                break;
            case 'execute':
                $char = 'E';
                break;
            case 'print':
                $char = 'F';
                break;
            case 'to_out':
                $char = 'G';
                break;
            case 'to_in':
                $char = 'H';
                break;
            case 'print_def':
                $char = 'I';
                break;
            case 'user_2':
                $char = 'V';
                break;
            case 'user_3':
                $char = 'W';
                break;
            case 'finish':
                $char = 'P';
                break;
            case 'r_finish':
                $char = 'Q';
                break;
            case 'M':
            case 'N':
            case 'J':
                $char = false;
                break;
        }
        return $char;
    }


}
