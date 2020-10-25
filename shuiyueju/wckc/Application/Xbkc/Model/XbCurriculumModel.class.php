<?php
namespace Xbkc\Model;
use Think\Model;


/**
 * 校本课程模型
 */
class XbCurriculumModel extends Model{
	//protected $tableName='xb_curriculum';
    protected $_validate = array(
        array('cnmae','require','课程程名称必填',self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cname', '1,100', '标题长度不合法', self::EXISTS_VALIDATE, 'length'),
        array('teacher', '1,10', '教师长度不合法', self::EXISTS_VALIDATE, 'length'),
        array('teacher','require','教师必填'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_BOTH),
        array('uid', 'is_login',3, 'function'),
    );

}