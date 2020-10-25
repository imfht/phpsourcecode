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
 * 权限设置-服务类
 * 
 * @author 牧羊人
 * @date 2018-12-12
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\MenuModel;
use app\admin\model\AdminRoleModel;
use app\admin\model\AdminModel;
use app\admin\model\AdminRomModel;
class AdminAuthService extends AdminServiceModel
{
    /**
     * 初始化模型
     * 
     * @author 牧羊人
     * @date 2018-12-12
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::initialize()
     */
    function initialize()
    {
        parent::initialize();
        $this->model = new MenuModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-12-12
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::getList()
     */
    function getList()
    {
        $param = input("request.");
        $type = (int)$param['type'];
        $typeId = (int)$param['type_id'];
        $list = $this->model->getChilds(0,false);
        if($list) {
            foreach ($list as &$val) {
                foreach ($val['children'] as &$vt) {
                    foreach ($vt['children'] as &$vo) {
                        $id = $vo['id'];
        
                        if($type==1) {
                            //角色
        
                            //获取权限点
                            $adminRoleMod = new AdminRoleModel();
                            $roleInfo = $adminRoleMod->getInfo($typeId);
                            $roleList = $roleInfo['auth'];
                            $funcList = $roleList[$id];
        
                        }else if($type==2) {
                            //人员
        
                            //获取权限点
                            $adminMod = new AdminModel();
                            $roleInfo = $adminMod->getInfo($typeId);
                            $roleList = $roleInfo['auth'];
                            $funcList = $roleList[$id];
        
                        }
        
                        foreach ($vo['funcList'] as &$v) {
                            if(in_array($v["id"],$funcList)){
                                $v['selected'] = 1;
                            }
                        }
        
                    }
                }
            }
        
        }
        return message("操作成功",true,$list);
    }
    
    /**
     * 保存设置权限
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function setAuth()
    {
        $post = input('post.', '', 'trim');
        //角色ID
        $type = (int)$post['type'];
        $typeId = (int)$post['type_id'];
        $auth = $post["auth"];
        if(!$type) {
            return message('类型不能为空',false);
        }
        if(!$typeId) {
            return message('类型ID不能为空',false);
        }
        
        //获取节点权限
        $list = array();
        if(is_array($auth)) {
            $result = array_keys($auth);
            if(is_array($result)) {
                foreach ($result as $val) {
                    $itemArr = explode(',', $val);
                    $list[$itemArr[0]][] = $itemArr[1];
                }
            }
        }
        
        //删除现有数据
        $adminRomMod = new AdminRomModel();
        $adminRomList = $adminRomMod->where(['type'=>$type,'type_id'=>$typeId])->select();
        if($adminRomList) {
            foreach ($adminRomList as $val) {
                $adminRomMod->drop($val['id']);
            }
        }
        
        //遍历最新的数据源
        $num = 0;
        $authStr = null;
        if(is_array($list)) {
            //序列化数组
            $authStr = serialize($list);
        
            //数据处理
            foreach ($list as $menuId=>$val) {
                if(!$menuId) continue;
        
                //重复性验证
                $info = $adminRomMod->where([
                    'type'      =>$type,
                    'type_id'   =>$typeId,
                    'menu_id'   =>$menuId
                ])->find();
        
                $func = "";
                if(is_array($val)) {
                    $func = implode(',', $val);
                }
        
                $data = [
                    'id'=>$info['id'],
                    'type'=>$type,
                    'type_id'=>$typeId,
                    'menu_id'=>$menuId,
                    'func_node'=>$func,
                    'mark'=>1,
                ];
                $rowId = $adminRomMod->edit($data);
                if($rowId) {
                    $num++;
                }
            }
        }
        
        if($type==1) {
            //角色权限配置
            $authMod = new AdminRoleModel();
        }else if($type==2) {
            //人员权限配置
            $authMod = new AdminModel();
        }
        $item = [
            'id'    =>$typeId,
            'auth'  =>$authStr,
        ];
        $error = '';
        $rowId = $authMod->edit($item,$error);
        if($rowId) {
            return message();
        }
        return message($error,false);
    }
    
}