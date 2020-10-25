<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理(SPU)
 */
namespace app\green\model;
use think\Model;

class GreenShop extends Model{
    
    protected $autoWriteTimestamp = true;
    protected $createTime = false;


    /**
     * 搜索封装
     */
    public function searchNameAttr($query, $value, $data){
        if(empty($value)){
            return;
        }
        $query->where('name','like','%' . $value . '%');
    }
        
    /**
     * 读取商品列表
     * @param string $status
     * @param string $keyword
     * @return void
     */
    public static function list(int $miniapp_id,string $status,string $keyword = null,$input = null){
        $condition[]  = ["member_miniapp_id","=",$miniapp_id];
        switch ($status) {
            case 'trash':  //回收站
                $condition[] = ["is_del","=",1];
                break; 
            case 'off_sale': //在售
                $condition[] = ["is_del","=",1];
                $condition[] = ["is_del","=",0];
                break;           
            case 'on_sale': //下架
                $condition[] = ["is_del","=",0];
                $condition[] = ["is_del","=",0];
                break;  
            default:
                $condition[] = ["is_del","=",0];
                break;
        }
        return self::where($condition)->withSearch(['name'],['name' => $keyword])->order('id desc')->paginate(20,false,['query'=>['status' => $status,'keyword' => $keyword,'input'=> $input]]);
    }

    /**
     * 检测单个SPU商品是否上架
     * @param integer $spu_id
     * @return void
     */
    public static function isSell(int $spu_id){
        return  self::where(['id' => $spu_id,'is_sale'=>2])->find();
    }

    //添加或编辑
    public static function edit(int $miniapp_id,array $param){
        $data['member_miniapp_id'] = $miniapp_id;
        $data['name']              = $param['name'];
        $data['note']              = $param['note'];
        $data['points']            = (int)$param['points'];
        $data['content']           = $param['content'];
        $data['img']               = $param['img'];
        $data['imgs']              = json_encode($param['imgs']);
        $data['update_time']       = time();
        if(isset($param['id'])){
            $id = (int)$param['id'];
            self::where('id',$id)->update($data);
            return $id;
        }else{
            $data['is_sale'] = 0;
            return self::insertGetId($data);
        }
    } 

    //批量操作
    public static function ids_action(int $is_sale,string $ids){ 
        switch ($is_sale) {
            case 1:
                $data['is_sale'] = 1; //回收站
                break;
            case 2:
                $data['is_del']  = 0;  //恢复
                $data['is_sale'] = 0;  //下架
                break;
            default:
                $data['is_sale'] = 0;  //下架
                break;
        }
        return self::whereIn('id',ids($ids))->data($data)->update(); //操作所有SPU商品
    }  

    /**
     * 删除SPU商品
     *
     * @param [type] $id
     * @param [type] $ids
     * @return void
     */
    public static function spu_delete(int $id,$ids){
        if(!empty($id)){
            $ids = (int)$id;
        }elseif(!empty($ids)){
            $ids = ids($ids);
        }else{
            return false;
        }
        $rel = self::whereIn('id',$ids)->field('is_del,id,imgs,img,content')->select()->toArray();
        if(!empty($rel)){
            $del_data = [];
            $up_data  = [];
            foreach ($rel as $key => $value) {
                if($value['is_del'] == 1){
                    $del_data[] = $value['id'];
                    $imgs[]     = json_decode($value['imgs']);
                    $content[]  = $value['content'];
                    $src[]      = $value['img'];
                }else{
                    $up_data[] = $value['id'];
                }
            }
            if(!empty($del_data)){
                $img = [];
                array_walk_recursive($imgs, function($value) use (&$img) {
                    array_push($img,$value);
                });         
                $imgc = [];
                array_walk_recursive($content, function($value) use (&$img) {
                    array_push($img, $value);
                });
                $imgfile = [];
                foreach ($img as $key => $value) {
                    foreach ($src as $rs) {
                        if($value != $rs){
                            $str = PATH_PUBLIC.$value;
                            if(file_exists($str)){
                                unlink($str);
                            }
                        }
                    }
                }
                self::whereIn('id',$del_data)->delete();
            }
            if(!empty($up_data)){
                self::whereIn('id',$up_data)->update(['is_del' => 1]);
            }
        }
        return true;
    }
}
