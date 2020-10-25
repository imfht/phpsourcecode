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
        )
    );

    protected $_validate = array(
        array('relation_model', 'require', '关联模型不能为空！', 1, 'regex', 3),
        array('name', 'require', '栏目名称不能为空！', 1, 'regex', 3),
        array('page_type', 'require', '页面展示类型不能为空！', 1, 'regex', 3),


        array('name', '0,30', '栏目名称长度不能超过30个字符！', 1, 'length', 3),
        array('sort', 'number', '栏目排序必须是整数！', 1, 'regex', 3),

        array('page_size', 'number', '每页显示文章数量必须是整数！', 1, 'regex', 3),

    );

    /**
     * 字段自动完成
     */
    protected $_auto = array(

        array('page_size','setPageSize',3,'callback'),

    );

    function setPageSize($page_size){
        if(empty($page_size)){
            return 10;
        }
        return $page_size;
    }

} 