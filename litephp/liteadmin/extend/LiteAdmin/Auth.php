<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/7
 * Time: 18:11
 */

namespace LiteAdmin;
use app\common\model\SystemAuthMap;
use app\common\model\SystemAuthNode;
use think\facade\Session;

/**
 * 权限验证类
 * Class Auth
 * @package LiteAdmin
 */
class Auth {

    /**
     * 执行验证
     * @param $path
     * @return bool|\think\response|\think\response\Redirect
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function auth($path) {

        $username = Session::get('admin.username');

        if ($username === "admin"){
            return true;
        }

        $admin_id = Session::get('admin.id');

        $node = SystemAuthNode::where('path',$path)->find();

        if (!$node || !in_array($node['auth'],[0,1,2])){
            return false;
//            halt('当前PATH（'.$path.'）没有加入权限管理列表');
        }

        switch (intval($node['auth'])){
            case 0:     // 免登录
                return true;
                break;
            case 1:     // 验证登录
                return !!$admin_id;
                break;
            case 2:     // 验证授权
                $access = self::getAllAcess();
                return in_array($node['id'],$access);
                break;
        }
        return false;
//        halt('当前PATH（'.$path.'出现了错误的授权代码');
    }

    /**
     * 获取当前用户全部权限 节点ID
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllAcess(){

        static $access;

        if (empty($access)){

            $admin_id = Session::get('admin.id');

            $roles = SystemAuthMap::alias('m')
                ->join('__SYSTEM_ROLE__ r','r.id=m.role_id')
                ->where('m.admin_id',$admin_id)
                ->select();

            $access = [];

            foreach ($roles as $role) {
                $access = array_merge($access,explode(',',$role['access_list']));
            }

            $access = array_unique($access);
        }

        return $access;
    }
}