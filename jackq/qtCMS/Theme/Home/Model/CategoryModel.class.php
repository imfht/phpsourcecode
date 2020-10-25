<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:43
 */

namespace Home\Model;


class CategoryModel extends CommonModel{

    // realtions
    protected $_link = array(
        // 一个栏目属于一个父节点
        'SubCategory' => array(
            'mapping_type' =>self::HAS_MANY,
            'class_name' => 'Category',
            'parent_key' => 'pid',
            'mapping_name' => 'subCategorys',
            'mapping_order' => 'sort',
            'condition' => 'is_show = 1'
        )
    );



} 