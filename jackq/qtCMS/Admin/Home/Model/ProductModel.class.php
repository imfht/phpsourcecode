<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:43
 */

namespace Home\Model;


class ProductModel extends CommonModel{

    // realtions
    protected $_link = array(
        // 一个文章属于一个栏目
        'category' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Category',
            'foreign_key' => 'category_id',
            'mapping_fields' => 'id,name'
        )
    );

    /**
     * 字段自动完成
     */
    protected $_auto = array(
        // 创建时间
        array('public_time', 'time', 1, 'function'),
        // 更新时间
        array('update_time', 'time', 3, 'function'),

        array('recommend','setRecommend',1,'callback'),
    );

    protected $_validate = array(

        array('name', 'require', '名称不能为空！', 1, 'regex', 3),
        array('category_id', 'require', '类型不能为空！', 1, 'regex', 3),

        array('name', '0,32', '名称不能超过32个字符！', 1, 'length', 3),


    );

    function setRecommend($recommend){
        if(empty($recommend)){
            return 2;
        }
    }


} 