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
 * 角色管理-模型
 * 
 * @author 牧羊人
 * @date 2018-07-13
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class AdminRoleModel extends CBaseModel {
    function __construct() {
        parent::__construct('admin_role');
    }
    
    //自动验证
    protected $_validate = array(
        array('name', 'require', '角色名称不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('name', '1,30', '角色名称长度不合法', self::EXISTS_VALIDATE, 'length',3),
    );
    
    function getInfo($id) {
        $info = parent::getInfo($id,true);
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
     * @date 2018-03-27
     */
    function getRoleAuth($roleIds) {
        if(is_array($roleIds)) {
            $list = array();
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