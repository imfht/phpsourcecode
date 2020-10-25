<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;
/**
 * 菜单模型
 * @author jry <598821125@qq.com>
 */
class SystemMenuModel extends Model{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title','require','菜单标题必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '菜单标题长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        array('utime', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 根据id获取当前菜单的顶级菜单
     * @author jry <598821125@qq.com>
     */
    public function getRootMenuById($id){
        if(empty($id)){
            return false;
        }
        switch(MODULE_NAME){
            case 'Admin': //系统菜单
                $map['id'] = array('eq', $id);
                $map['status'] = array('eq', 1);
                $main_menu = array();
                do{
                    $main_menu = $this->where($map)->find();
                    $map['id'] = array('eq', $main_menu['pid']);
                }while($main_menu['pid'] > 0);
                break;
            default: //模块菜单
                $map = array('id'=> $id);
                $main_menu = array();
                $tree = new \Common\Util\Tree();
                $current_module_admin_menu = json_decode(D('SystemModule')->getFieldByName(MODULE_NAME, 'admin_menu'), true);
                do{
                    $main_menu = $tree->list_search($current_module_admin_menu, $map);
                    $main_menu = $main_menu[0];
                    $map = array('id'=> $main_menu['pid']);
                }while($main_menu !== null && $main_menu['pid'] != '0');
                break;
        }
        return $main_menu;
    }

    /**
     * 获取当前菜单
     * @author jry <598821125@qq.com>
     */
    public function getCurrentMenu(){
      switch(MODULE_NAME){
            case 'Admin': //系统菜单
                $map['status'] = array('eq', 1);
                $map['url'] = array('like', '/'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'%');
                $result = $this->where($map)->order('pid desc')->find();
                break;
            default: //模块菜单
                $menus = json_decode(D('SystemModule')->getFieldByName(MODULE_NAME, 'admin_menu'), true);
                foreach($menus as $key => $val){
                    if($val['url']){
                        $config_url = U($val['url']);
                        $current_url = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
                        if($config_url === $current_url){
                            $result = $val;
                        }
                    }
                }
                break;
        }
        return $result;
    }

    /**
     * 根据菜单ID的获取其所有父级菜单
     * @param array $current 当前菜单信息
     * @return array 父级菜单集合
     * @author jry <598821125@qq.com>
     */
    public function getParentMenu($current){
        if(empty($current)){
            return false;
        }
      switch(MODULE_NAME){
            case 'Admin': //系统菜单
                $map['status'] = array('eq', 1);
                $menus = $this->where($map)->select();
                $pid   = $current['pid'];
                $temp  = array();
                $res[] = $current;
                while(true){
                    foreach ($menus as $key => $val){
                        if($val['id'] == $pid){
                            $pid = $val['pid'];
                            array_unshift($res, $val); //将父菜单插入到数组第一个元素前
                        }
                    }
                    if($pid == '0'){
                        break;
                    }
                }
                break;
            default: //模块菜单
                $menus = json_decode(D('SystemModule')->getFieldByName(MODULE_NAME, 'admin_menu'), true);
                $pid   = $current['pid'];
                $temp  = array();
                $res[] = $current;
                while(true){
                    foreach ($menus as $key => $val){
                        if($val['id'] == $pid){
                            $pid = $val['pid'];
                            array_unshift($res, $val); //将父菜单插入到数组第一个元素前
                        }
                    }
                    if($pid == '0'){
                        break;
                    }
                }
                break;
        }
        return $res;
    }
}
