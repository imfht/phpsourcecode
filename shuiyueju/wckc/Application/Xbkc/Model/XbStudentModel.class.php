<?php
namespace Xbkc\Model;
use Think\Model\RelationModel;

/**
 * 学生模型
 * @author 水月居 <singliang@163.com>
 */

class XbStudentModel extends RelationModel {
//Relation
    protected $_validate = array(
        array('name', '1,10', '姓名长度为1-10个字符', self::EXISTS_VALIDATE, 'length'),
        array('name', '', '姓名已经被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
    );

    protected $_link = array(
        'Xb_curriculum'=>array(
             'mapping_type'=> self::BELONGS_TO,
             'class_name' => 'Xb_curriculum',
		     'mapping_fields'=>'cname,teacher',
		     //'mapping_name'  => 'cname',
             'foreign_key' => 'cid',
             'as_fields'=>'cname,teacher',
		),
   
    );

    public function getName($sid){
        return $this->where(array('uid'=>(int)$sid))->getField('name');
    }

}
