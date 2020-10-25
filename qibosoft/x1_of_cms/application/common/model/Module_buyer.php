<?php
namespace app\common\model;

use think\Model;

/**
 *商家购买的应用模块
 */
class Module_buyer extends Model
{
    protected $table = '__MODULE_BUYER__';
	
	//主键不是ID,要单独指定
	//public $pk = 'gid';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
}