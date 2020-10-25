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
 * 角色-模型
 * 
 * @author 牧羊人
 * @date 2018-12-10
 */
namespace app\admin\model;
use app\common\model\BaseModel;
class AdminRoleModel extends BaseModel
{
    //设置数据表
    protected $name = 'admin_role';
    
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

            //权限反序列化
            if($info['auth']) {
                $info['auth'] = unserialize($info['auth']);
            }
            
        }
        return $info;
    }
    
    /**
     * 获取角色权限
     * 
     * @author 牧羊人
     * @date 2018-12-10
     * @param unknown $roleIds
     * @return Ambigous <multitype:multitype: , unknown>
     */
    function getRoleAuth($roleIds) 
    {
        $list = [];
        if(is_array($roleIds)) {
            foreach ($roleIds as $val) {
                $info = $this->getInfo($val);
                $auth = $info['auth'];
                if(is_array($auth)) {
                    foreach ($auth as $kt=>$vt) {
                        if(!in_array($kt, array_keys($list))) {
                            $list[$kt] = array();
                        }
                        foreach ($vt as $v) {
                            if(!in_array($v, $list[$kt])) {
                                $list[$kt][] = $v;
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }
    
}