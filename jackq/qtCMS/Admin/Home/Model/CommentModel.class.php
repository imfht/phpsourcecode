<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:43
 */

namespace Home\Model;


class CommentModel extends CommonModel{

    // realtions
    protected $_link = array(
        // 一个文章属于一个栏目
        'user' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'User',
            'foreign_key' => 'user_id',
            'mapping_fields' => 'id,username,reallyname,photo'
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
        // 文章内容
        //array('content', 'htmlspecialchars', 3, 'function'),
    );

   /* protected $_validate = array(


        array('name', 'require', '职位名称不能为空！', 1, 'regex', 3),
        array('address', 'require', '工作地点不能为空！', 1, 'regex', 3),
        array('people_num', 'require', '人数不能为空！', 1, 'regex', 3),
        array('content', 'require', '工作要求不能为空！', 1, 'regex', 3),

        array('name', '0,50', '职位名称长度不能超过50个字符！', 1, 'length', 3),
        array('address', '0,50', '工作地点长度不能超过50个字符！', 1, 'length', 3),
        array('people_num', 'number', '人数必须是整数！', 1, 'regex', 3),





    );*/

} 