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
 * 人员管理-模型
 * 
 * @author 牧羊人
 * @date 2018-06-21
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class AdminModel extends CBaseModel {
    public function __construct() {
        parent::__construct("admin");
    }
    
    //自动验证
    protected $_validate = array(
//         array('realname','','真实姓名已经存在！',self::EXISTS_VALIDATE,'unique',1),//新增数据时验证
        array('realname', '1,30', '真实姓名长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('mobile','/^1[3|4|5|7|8][0-9]\d{4,8}$/','手机号码不正确！','0','regex',1),
//         array('email', '', '邮箱已经存在！', 0, 'unique', 1),//新增数据时验证
        array('email', 'email', '邮箱不合法！', 0, '', 3),
    );
   
    //自动完成
    protected $_auto = array (
        array('add_time','time',3,'function'), // 对add_time字段在更新的时候写入当前时间戳
        array('upd_time','time',3,'function'),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-12
     */
    public function getInfo($id,$flag=false) {
        $info = parent::getInfo($id);
        if($info) {
            
            //头像
            if($info['avatar']) {
                $info['avatar_url'] = IMG_URL . $info['avatar'];
            }
            
            //入职日期
            if($info['entry_date']) {
                $info['format_entry_date'] = date('Y-m-d H:i:s',$info['entry_date']);
            }
            
            //性别
            $info['gender_name'] = C("GENDER_ARR")[$info['gender']];
            
            //职位
            if($info['position_id']) {
                $positionMod = M("adminPosition");
                $positionInfo = $positionMod->find($info['position_id']);
                $info['position_name'] = $positionInfo['name'];
            }
            
            //获取组织
            if($info['organization_id']) {
                $adminOrgMod = new AdminOrgModel();
                $adminOrgInfo = $adminOrgMod->getInfo($info['organization_id']);
            }
            
            //获取部门
            if($info['dept_id']) {
                $adminDepMod = new AdminDepModel();
                $adminDepInfo = $adminDepMod->getInfo($info['dept_id']);
                $adminDepName = $adminDepMod->getDepName($info['dept_id'],">>");
                $info['dept_name'] = $adminOrgInfo['name'] . ">>" . $adminDepName;
            }
            
            if($flag) {
                
                //独立权限反序列化
                if($info['auth']) {
                    $auth = unserialize($info['auth']);
                }
                $info['auth'] = $auth;
                
                //角色权限
                if($info['role_ids']) {
                    $roleIds = explode(',', $info['role_ids']);
                    $adminRoleMod = new AdminRoleModel();
                    $roleAuth = $adminRoleMod->getRoleAuth($roleIds);
                }
                
                //独立权限、角色权限重组成人员权限
                $authList = array();
                
                //独立权限
                if(is_array($auth)) {
                    foreach ($auth as $key=>$val) {
                        $authList[$key][] = $val;
                    }
                }
                
                //角色权限
                if(is_array($roleAuth)) {
                    foreach ($roleAuth as $kt=>$vt) {
                        $authList[$kt][] = $vt;
                    }
                }
                
                //组织权限
                $orgAuth = $adminOrgInfo['auth'];
                if(is_array($orgAuth)) {
                    foreach ($orgAuth as $ko=>$vo) {
                        $authList[$ko][] = $vo;
                    }
                }
                
                //部门权限
                $depAuth = $adminDepInfo['auth'];
                if(is_array($depAuth)) {
                    foreach ($depAuth as $k=>$v) {
                        $authList[$k][] = $v;
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
    
    /**
     * 获取所有人员
     *
     * @author 牧羊人
     * @date 2018-07-12
     */
    function getAll() {
        $data = $this->getCache("adminAll");
        if(!$data) {
            $result = $this->where(['status'=>1,'mark'=>1])->getField("id,realname,gender");
            $this->setCache("adminAll", $result);
        }
        return $data;
    }
    
}