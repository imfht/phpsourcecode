<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理(SPU)
 */
namespace app\fastshop\model;
use think\Model;
use filter\Filter;

class Item extends Model{
    
    protected $pk    = 'id';
    protected $table  = 'ai_fastshop_item';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;

    /**
     * 商品基础库
     * @return void
     */
    public function Category(){
        return $this->hasOne('Category','id','category_id');
    }

    /**
     * 搜索封装
     */
    public function searchNameAttr($query, $value, $data){
        $query->where('name','like','%' . $value . '%');
    }

   /**
     * 查询单个商品（包裹委托价格）
     * @param string $status
     * @param string $keyword
     * @return void
     */
    public function getfind(int $id){
        return self::view('fastshop_item','*')
        ->view('fastshop_entrust', 'entrust_price','fastshop_item.id = fastshop_entrust.item_id','left')
        ->where(['fastshop_item.id' => $id])
        ->find();
    }

        
    /**
     * 读取商品列表
     * @param string $status
     * @param string $keyword
     * @return void
     */
    public function list(int $miniapp_id,string $status,string $keyword = null,$input = null){
        switch ($status) {
            case 'trash':  //回收站
                $condition['is_sale'] = 1;
                break; 
            case 'off_sale': //在售
                $condition['is_sale'] = 2;
                break;           
            case 'on_sale': //在售
                $condition['is_sale'] = 0;
                break;  
            default:
                $condition[] = ['is_sale','<>',1];
                break;
        }
        if(!empty($keyword)){
            $keyword = Filter::filter_escape($keyword);
            $condition[] = ["name","like","%{$keyword}%"];
        }
        return self::where(['member_miniapp_id' => $miniapp_id])->where($condition)->order('id desc')->paginate(20,false,['query'=>['status' => $status,'keyword' => $keyword,'input'=> $input]]);
    }

    /**
     * 检测单个SPU商品是否上架
     * @param integer $spu_id
     * @return void
     */
    public function isSell(int $spu_id){
        return  self::where(['id' => $spu_id,'is_sale'=>2])->find();
    }

    //添加或编辑
    public function edit(int $miniapp_id,array $param){
        $data['member_miniapp_id'] = $miniapp_id;
        $data['category_id']       = (int)$param['category_id'];
        $data['category_path_id']  = $param['category_path_id'];
        $data['name']              = $param['name'];
        $data['types']             = $param['types'];
        $data['price']             = (float)$param['price']*100;
        $data['market_price']      = (float)$param['market_price'];
        $data['sell_price']        = (float)$param['sell_price'];
        $data['cost_price']        = (float)$param['cost_price'];
        $data['points']            = (int)$param['points'];
        $data['repoints']          = (int)$param['repoints'];
        $data['weight']            = (int)$param['weight'];
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
    public function ids_action(int $is_sale,string $ids){ 
        switch ($is_sale) {
            case 1:
                $data['is_sale'] = 1; //删除
                break;
            case 2:
                $data['is_sale'] = 2;  //在售
                break;
            default:
                $data['is_sale'] = 0;  //恢复商品到未审核状态
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
    public function spu_delete(int $id,$ids){
        if(!empty($id)){
            $ids = (int)$id;
        }elseif(!empty($ids)){
            $ids = ids($ids);
        }else{
            return false;
        }
        $rel = self::whereIn('id',$ids)->field('is_sale,id,imgs,img,content')->select()->toArray();
        if(!empty($rel)){
            $del_data = [];
            $up_data  = [];
            foreach ($rel as $key => $value) {
                if($value['is_sale'] == 1){
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
                self::whereIn('id',$up_data)->update(['is_sale' => 1]);
            }
        }
        return true;
    }
}
