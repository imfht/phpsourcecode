<?php
namespace app\common\model;

use think\Model;

/**
 * 用户升级认证记录
 * @package app\admin\model
 */
class Grouplog extends Model
{
    protected $table = '__GROUPLOG__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;


	
}