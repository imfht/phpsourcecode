<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 管理人员-模型
 * 
 * @author 牧羊人
 * @date 2018-12-08
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class AdminModel extends BaseModel
{
    // 设置数据表
    protected $name = 'admin';
    
//     // 验证规则
//     protected $rule = [
//         'name|帐号'       => 'require|unique:admin_users',
//         'parent_id|角色'  => 'require',
//         'password|密码'   => 'require',
//         'nickname|昵称'   => 'require',
//         'mobile|手机号'   => ['require', 'regex' => '/^1(3|4|5|7|8)[0-9]\d{8}$/'],
//         'email|邮箱'      => 'email',
//         'status|是否启用' => 'require',
//     ];
    
//     // 提示语
//     protected $message = [
//         'email.email'  => '邮箱格式错误',
//         'mobile.regex' => '手机格式错误',
//     ];
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-10
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
            // 头像
            if($info['avatar']) {
                $info['avatar_url'] = IMG_URL . $info['avatar'];
            }
            
            // 入职日期
            if($info['entry_date']) {
                $info['format_entry_date'] = date('Y-m-d H:i:s',$info['entry_date']);
            }
            
            // 性别
            $info['gender_name'] = Config::get('GENDER_ARR')[$info['gender']];
            
            // 职位
            if($info['position_id']) {
                $positionMod = new AdminPositionModel();
                $positionInfo = $positionMod->find($info['position_id']);
                $info['position_name'] = $positionInfo['name'];
            }
            
            // 获取组织
            if($info['organization_id']) {
                $adminOrgMod = new AdminOrgModel();
                $adminOrgInfo = $adminOrgMod->getInfo($info['organization_id']);
            }
            
            // 获取部门
            if($info['dept_id']) {
                $adminDepMod = new AdminDepModel();
                $adminDepName = $adminDepMod->getDepName($info['dept_id'],">>");
                $info['dept_name'] = $adminOrgInfo['name'] . ">>" . $adminDepName;
            }
            
            // 权限处理
            if(true) {
            
                // 独立权限反序列化
                if($info['auth']) {
                    $auth = unserialize($info['auth']);
                }
                $info['auth'] = $auth;
            
                // 角色权限
                if($info['role_ids']) {
                    $roleIds = explode(',', $info['role_ids']);
                    $adminRoleMod = new AdminRoleModel();
                    $roleAuth = $adminRoleMod->getRoleAuth($roleIds);
                }
            
                // 权限重组
                $authList = [];
            
                // 独立权限
                if(is_array($auth)) {
                    foreach ($auth as $key=>$val) {
                        $authList[$key][] = $val;
                    }
                }
            
                // 角色权限
                if(is_array($roleAuth)) {
                    foreach ($roleAuth as $kt=>$vt) {
                        $authList[$kt][] = $vt;
                    }
                }
                $result = array();
                foreach ($authList as $key=>$val) {
                    if(!in_array($key, array_keys($result))) {
                        $result[$key] = array();
                    }
                    foreach ($val as $vt) {
                        foreach ($vt as $v) {
                            if(!in_array($v, $result[$key])) {
                                $result[$key][] = $v;
                            }
                        }
                    }
                }
                $info['adminAuth'] = $result;
            }
            
        }
        return $info;
    }
    
}