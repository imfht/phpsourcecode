<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类管理
 */
namespace app\popupshop\model;
use think\Model;
use category\Tree;

class Category extends Model{

    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_category';

    //添加或编辑
    public static function edit($param){
        $data['parent_id'] = $param['parent_id'];
        $data['title']     = $param['title'];
        $data['name']      = $param['name'];
        $data['sort']      = $param['sort'];
        $data['picture']   = $param['picture'];
        $data['types']     = $param['types'];
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
    public static function selectPath(int $miniapp_id,$parent_id) {
        $pathMaps[] = ['name'=>'根目录','url'=>url('popupshop/category/index')];
        $getPath = self::getPath($miniapp_id,$parent_id);
        foreach ($getPath as $value) {
            $pathMaps[] = ['name' => $value['title'],'url' => url('popupshop/category/index',['parent_id'=>$value['id']])];
        }
        return $pathMaps;
    }

    /**
     * 获取当前路径
     * @param type $parent_id
     * @return type
     */
    public static function getPath($miniapp_id,$parent_id){
        $result = self::field('id,title,parent_id')->where(['member_miniapp_id' => $miniapp_id])->select();
        $tree =  new Tree(array('id','parent_id','title','name'));
        return $tree->getPath($result,$parent_id);
    }
}