<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类管理
 */
namespace app\green\model;
use think\Model;
use category\Tree;

class GreenCategory extends Model{

    //添加或编辑
    public function edit($param){
        $data['parent_id'] = $param['parent_id'];
        $data['title']     = trim($param['title']);
        $data['name']      = trim($param['name']);
        $data['sort']      = trim($param['sort']);
        $data['picture']   = trim($param['picture']);
        $data['price']     = trim($param['price']);
        $data['update_time']  = time();
        if(isset($param['id'])){
            return self::update($data,['id'=>(int)$param['id']]);
        }else{
            if($param['parent_id']){
                $info =  self::get($param['parent_id']);
                if($info['root_id']){
                    $data['root_id'] = $info['root_id'];
                }else{
                    $data['root_id'] = $info['id'];
                }
            }else{
                $data['root_id']     = 0;
            }
            $data['create_time']       = time();
            $data['member_miniapp_id'] = $param['member_miniapp_id'];
            return self::insert($data);
        }
    }

    /**
     * 获取访问路径
     * @param int $parent_id
     */
    public function selectPath($parent_id) {
        $pathMaps[] = ['name'=>'旧物分类','url'=>url('category/index')];
        $getPath = self::getPath($parent_id);
        foreach ($getPath as $value) {
            $pathMaps[] = ['name' => $value['title'],'url' => url('category/index',['parent_id'=>$value['id']])];
        }
        return $pathMaps;
    }

    /**
     * 获取当前路径
     * @param type $parent_id
     * @return type
     */
    public function getPath($parent_id){
        $result = self::field('id,title,parent_id')->select();
        $tree =  new Tree(array('id','parent_id','title','name'));
        return $tree->getPath($result,$parent_id);
    }
}