<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:43
 */

namespace Home\Model;


class ArticleModel extends CommonModel{

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

        array('come_from','setCome_from',3,'callback'),

        array('is_slide','setSlide',3,'callback'),

        array('open_comment','setOpen_comment',3,'callback'),

        array('target_type','setTarget_type',3,'callback'),

    );

    protected $_validate = array(
        // 栏目不能为空
        array('category_id', 'require', '栏目不能为空！', 1, 'regex', 3),
        //文章内容不能为空
        array('title', 'require', '文章标题不能为空！', 1, 'regex', 3),
        array('title', '0,200', '文章标题长度不能超过200个字符！', 1, 'length', 3),
        //文章内容不能为空
        array('content', 'require', '文章内容不能为空！', 1, 'regex', 3),
    );

    function  setSlide($slide){
        if(empty($slide)){
            return 2;
        }
        return $slide;
    }

    function setOpen_comment($open_comment){
        if(empty($open_comment)){
            return 2;
        }
        return $open_comment;
    }

    function setTarget_type($target_type){
        if(empty($target_type)){
            return "_self";
        }
        return $target_type;
    }

    function setCome_from($come_from){
        if(empty($come_from)){
            return "本站";
        }
        return $come_from;
    }



} 