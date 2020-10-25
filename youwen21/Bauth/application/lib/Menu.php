<?php
namespace app\lib;

/**
 * 菜单模块
 */
class Menu
{

    //config 
    private $_rootPower = FALSE;
    private $_ids = '';
    private $_initMap = '';

    private $_dbMenu;

    public function __construct($rootPower=FALSE, $ids='', $useMap=true, $map = [])
    {
        $this->_rootPower = $rootPower;
        $this->_ids = is_array($ids)?implode(',', $ids):$ids;
        if($useMap){
            $this->_initMap = array_merge(['hide'=> 0, 'is_dev' =>0], $map);
        }
        $this->_dbMenu = db('menu');
    }

    // 指定只获取这些ID中符合条件的菜单
    public function condition($rootPower=FALSE, $ids='')
    {
        $this->_rootPower = $rootPower;
        if(is_array($ids)){
            $this->_ids = implode(',', $ids);
        }else{
            $this->_ids = $ids;
        }
        return $this;
    }

    // 获取当前请求URL的ID
    public function getId($module, $controller, $action)
    {
        $map = [];
        $map['module'] = $module;
        $map['controller'] = $controller;
        $map['action'] = $action;
        $ret = $this->_dbMenu->field('id')->where($map)->find();
        return $ret && $ret['id'] ? $ret['id']:0;
    }

    // 获取当前请求URL的PID
    public function getRootId($reqId, $maxLevel=5)
    {
        // if($maxLevel == 0){
        //     return 0; 
        // }
        // $ret = $this->_dbMenu->field('id,pid')->where('id='.$reqId)->find();
        // if($ret && $ret['pid']){
        //     return $this->getRootId($ret['pid'], $maxLevel--);
        // }else{
        //     return isset($ret['id'])?$ret['id']:0;
        // }
        $reqRootId = 0;

        $ret = $this->_getrootId($reqId);
        if($ret['code']){
            $reqRootId = $ret['pid'];
        }else{
            $ret2 = $this->_getrootId($ret['pid']);
            if($ret2['code']){
                $reqRootId = $ret2['pid'];
            }else{
                $ret3 = $this->_getrootId($ret2['pid']);
                if($ret3['code']){
                    $reqRootId = $ret3['pid'];
                }else{
                    $reqRootId = 0;
                }
            }
        }

        return $reqRootId;
    }

    private function _getrootId($reqId)
    {
        $ret = $this->_dbMenu->field('id,pid')->where('id='.$reqId)->find();
        if($ret){
            if($ret['pid']){
                return ['code'=>0, 'pid'=>$ret['pid']];
            }else{
                return ['code'=>1, 'pid'=>$ret['id']];
            }
        }
        return ['code'=>1, 'pid'=>0];
    }

    // 获取指定IDS的菜单列表
    public function getMenu($module='admin', $condition = [])
    {
        $intMap = $this->_Map();
        $map = array_merge($intMap, $condition);
        $map['module'] = $module;
        $ret = $this->_dbMenu->where($map)->select();
        return $ret;
    }

    // 条件生成
    private function _Map()
    {
        $map = [];
        if(!$this->_rootPower){
            $map['id'] = ['in', $this->_ids];
        }
        if(!empty($this->_initMap)){
            $map = array_merge($this->_initMap, $map);
        }
        return $map;
    }

    // 获取指定IDS的顶级菜单
    public function getTopMenu($module = 'admin')
    {
        $map = $this->_Map();
        $map['pid'] = 0;
        $map['module'] = $module;
        if(!$this->_rootPower){
            $map['id'] = ['in', $this->_ids];
        }
        $tops = $this->_dbMenu->where($map)->order('sort')->select();
        return $tops;
    }

    // 获取指定PID的二级分组后菜单
    public function getSideTree($pid)
    {
        $sideMenu = $this->getSideMenu($pid);
        //获取菜单分组  取组，去空，去重复
        // $groups = array_unique(array_filter(array_column($sideMenu, 'group', 'id')));
        //分组二级菜单
        $sideTree = $this->_listToGroup($sideMenu);
        return $sideTree;
    }

    //按组分二级菜单
    private function _listToGroup($sideMenu)
    {
        $list = [];
        foreach ($sideMenu as $lk=>$lv){
            if($lv['hide'] == 1 || $lv['is_dev'] == 1){
                continue;
            }
            if(empty($lv['group'])){
                if($lv['hide']){
                    $list['hide']['group'] = '隐藏菜单';
                    $list['hide']['_child'][] = $lv;
                    continue;
                }else{
                    $list['ungrouped']['group'] = '未分组';
                    $list['ungrouped']['_child'][] = $lv;
                    continue;
                }
            }
            $key = md5(trim($lv['group']));
            $list[$key]['group'] = $lv['group'];
            $list[$key]['_child'][] = $lv;
        }
        // ksort($list, SORT_STRING);
        return $list;
    }

    // 获取指定PID的二级菜单列表
    public function getSideMenu($pid=0)
    {
        $map = $this->_Map();
        $map['pid'] = $pid;
        $sideMenu = $this->_dbMenu->where($map)->order('sort')->select();
        return $sideMenu;
    }

}