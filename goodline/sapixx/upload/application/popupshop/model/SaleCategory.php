<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类管理
 */
namespace app\popupshop\model;
use think\Model;

class SaleCategory extends Model{

    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_sales_category';

    //添加或编辑
    public static function edit($param){
        $data['title']       = $param['title'];
        $data['name']        = $param['name'];
        $data['picture']     = $param['picture'];
        $data['update_time'] = time();
        if(isset($param['id'])){
            return self::update($data,['id'=>(int)$param['id']]);
        }else{
            $data['create_time']       = time();
            $data['member_miniapp_id'] = $param['member_miniapp_id'];
            return self::insert($data);
        }
    }
}