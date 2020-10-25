<?php
namespace app\common\api;

use app\common\model\mup\MupUserBo;
use think\Config;
use think\Db;
use think\Log;
use think\Request;

class Para {
    /**
     * 系统定义的静态变量
     */
    public static function system_const_variable() {
        Common::def_system_var();
        // 上级执行的模块、控制器、动作
        // 一般用于 视图 中js获取对象
        define('P_M', input('p_m'));
        define('P_C', input('p_c'));
        define('P_A', input('p_a'));
        return;
    }

    /**
     * 用于系统记忆语言参数
     */
    public static function system_lang() {
        session('lang', input('get.lang'));
        cookie('think_var', input('get.lang'));  // cookie 中的多语言变量名
        return;
    }

    /**
     * 检测系统的公共参数
     * 有更改时需要重新初始化
     */
    public static function system_para() {
        $system_para = Db::query("select para_id,para_value from app_para");
        // 将序列数组变成键值对
        $system_para = array_key_val($system_para, 'para_id', 'para_value');
        cache('system_para', $system_para);
        return $system_para;
    }

    /**
     * 查找系统参数值
     * @param $para_id
     * @return bool
     */
    public static function system_para_get($para_id) {
        $sys_para = cache('system_para');
        isset($sys_para[$para_id]) ? $para_val = $sys_para[$para_id] : $para_val = false;
        return $para_val;
    }

    /**
     * 设置用户的参数
     * @param $login_info // 用户登录信息
     */
    public static function user_login($login_info) {
        session('user_type', $login_info['user_type']);
        session('user_id', $login_info['account_id']);
        session('user_name', $login_info['account_name']);
        session('user_old_pwd', $login_info['pwd']);
        session('is_admin', $login_info['is_admin']);
        // 不同类型的id与name已经session到user_id与user_name
        // 如果不同类型还需要缓存不同参数，可以单独进行查询并缓存
        if ('emp' == $login_info['user_type']) {
            //SESSION中的员工编号和考勤卡号，因为这两个用得比较多，所以Session化
            session('user_emp_id', $login_info['account_id']);
        }
    }

    /**
     * 缓存用户的各种权限进一步的限制
     * a 用户需要重启软件才生效
     * b 保存时刷新调用此方法
     * @param $user_id
     */
    public static function user_bo($user_id = null) {
        if ($user_id) {
            $where['user_id'] = $user_id;
        } else {
            $where['user_id'] = session('user_id');
        }
        // 限制缓存
        $mup_user_bo = new MupUserBo();
        $bo_data = $mup_user_bo->where($where)->select()->toArray();
        $bo_data = array_merge_column($bo_data, 'bo_id', 'val', true);
        // 处理缓存
        session('mup_user_bo', $bo_data);
    }

    /**
     * 用户是否有某个限制id
     * @param $bo_id
     * @return bool
     */
    public static function user_bo_has($bo_id) {
        $bo_data = session('mup_user_bo');
        if (empty($bo_data)) {
            return false;
        }
        $bo_column = array_column($bo_data, 'bo_id');
        if (in_array($bo_id, $bo_column)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 返回传递的用户bo_id对应的值
     * @param $bo_id
     * @return mixed
     */
    public static function user_bo_val($bo_id) {
        $bo_data = session('mup_user_bo');
        if (empty($bo_data)) {
            return false;
        }
        $bo_column = array_column($bo_data, 'bo_id');
        if (in_array($bo_id, $bo_column)) {
            $key = array_search($bo_id, $bo_column);
            return $bo_data[$key]['val'];
        } else {
            return false;
        }
    }

    /**
     * 根据用户与限制id,返回限制的数组
     * @param $user_id
     * @param $bo_id
     * @return array
     */
    public static function user_bo_val_now($user_id, $bo_id) {
        if ($user_id) {
            $where['user_id'] = $user_id;
        } else {
            $where['user_id'] = session('user_id');
        }
        if ($bo_id) {
            $where['bo_id'] = $bo_id;
        }
        // 限制缓存
        $mup_user_bo = new MupUserBo();
        $bo_data = $mup_user_bo->where($where)->field('val')->select()->toArray();
        if (empty($bo_data)) {
            return [];
        }
        $bo_val = array_column($bo_data, 'val');
        return $bo_val;
    }


}
