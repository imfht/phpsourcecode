<?php
namespace app\common\service;

use app\common\model\AbilitiesModel;
use app\common\model\RoleAbilitiesModel;
use think\Db;
use utils\JWTUtils;
use utils\TreeUtils;

class AbilitiesService
{
    protected $abilitiesModel;
    protected $roleAbilitiesModel;

    public function __construct()
    {
        $this->abilitiesModel = new AbilitiesModel();
        $this->roleAbilitiesModel = new RoleAbilitiesModel();
    }

    /**
     * [getLeftNavMenu 根据节点数据获取对应的菜单(两级)]
     * @author [haigle] [991382548@qq.com]
     * return array
     */
    public function getLeftNavMenu()
    {
        $result = $this->getMenu();
        $nav_menu = $this->prepareMenu($result);
        return $nav_menu;
    }

    /**
     * [getLeftMenu 根据节点数据获取对应的菜单(页面平铺显示全部)]
     * @author [haigle] [991382548@qq.com]
     * return array
     */
    public function getLeftMenu()
    {
        $result = $this->getMenu();
        if($result){
            $nav_menu = $this->leftRule($result);
            return $nav_menu;
        }
        return false;
    }

    /**
     * [getMenu 根据用户类型判断取菜单范围]
     * @author [haigle] [991382548@qq.com]
     * return array
     */
    public function getMenu()
    {
//        $user_id = session('auth')['id'];
        $user_id = JWTUtils::decode(session('auth'))->id;
        if($user_id == 1){
            return $this->abilitiesModel->getAllList();
        }else{

            $source_data = $this->abilitiesModel->getAllListByDate($user_id);
//            dump($source_data);
            return $source_data;
        }
    }

    /**
     * [save 保存与修改菜单提交数据]
     * @author [haigle] [991382548@qq.com]
     * return boole
     */
    public function save($data)
    {
        if(!empty($data['id'])){
            $this->abilitiesModel->saveDate($data);
            return true;
        }
        $this->abilitiesModel->insertDate($data);
        return true;
    }

    /**
     * [getFind 获取详细菜单栏]
     * @author [haigle] [991382548@qq.com]
     * return boole
     */
    public function getFind($id)
    {
        return $this->abilitiesModel->getFind($id);
    }

    /**
     * [del 根据主键ID删除单条数据]
     * @author [haigle] [991382548@qq.com]
     * return boole
     */
    public function del($id)
    {
        Db::startTrans();
        try {
            $this->abilitiesModel->deleteDate($id);
            $this->roleAbilitiesModel->deleteDateByAbilitiesId($id);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }


    /**
     * [prepareMenu 整理两级菜单树方法]
     * @param $param
     * @author [haigle] [991382548@qq.com]
     * @return array
     */
    function prepareMenu($param)
    {
        $parent = []; //父类
        $child = [];  //子类
        foreach($param as $key=>$vo){
            if($vo['parent_id'] == 1){
//                $vo['href'] = url($vo['href']);
                $parent[] = $vo;
            }else{
//                $vo['href'] = url($vo['href']); //跳转地址
                $child[] = $vo;
            }
        }
        foreach($parent as $key=>$vo){
            foreach($child as $k=>$v){
                if($v['parent_id'] == $vo['id']){
                    $parent[$key]['child'][] = $v;
                }
            }
        }
        unset($child);
        return $parent;
    }

    /**
     * [leftRule 整理菜单树方法]
     * @param $param
     * @author [haigle] [991382548@qq.com]
     * @return array
     */
    public function leftRule($cate , $lefthtml = '— — ' , $parent_id=0 , $lvl=0, $leftpin=0 ){
        $arr=array();
        foreach ($cate as $v){
            if($v['parent_id']==$parent_id){
                $v['lvl']=$lvl + 1;
                $v['leftpin']=$leftpin + 0;//左边距
                $v['lefthtml']=str_repeat($lefthtml,$lvl).$v['name'];
                $arr[]=$v;
                $arr = array_merge($arr,self::leftRule($cate,$lefthtml,$v['id'],$lvl+1 , $leftpin+20));
            }
        }
        return $arr;
    }

    /**
     * [roleAbilitiesTree zTree树结构]
     * @param $param
     * @author [haigle] [991382548@qq.com]
     * @return String
     */
    public function roleAbilitiesTree($id)
    {
        $result = $this->getMenu();
        $rule = $this->abilitiesModel->getRoleAbilities($id);
        $tree_utils = new TreeUtils();
        return $tree_utils->getNodeInfo($result, $rule);
    }

    public function changeRoleAbilities($role_id, $result)
    {
        $source = explode(',',$result);
        $data = array();
        foreach($source as $item){
            $data[] = ["role_id" => $role_id,"abilities_id" => $item,"created_at" => date("Y-m-d H:i:s",time())];
        }
        Db::startTrans();
        try {
            $this->roleAbilitiesModel->deleteDateByRoleId($role_id);
            $this->roleAbilitiesModel->insertDate($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }

}