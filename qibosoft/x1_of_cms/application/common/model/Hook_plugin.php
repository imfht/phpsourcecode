<?php
namespace app\common\model;

use think\Model;

/**
 * 钩子插件
 * @package app\admin\model
 */
class Hook_plugin extends Model
{
    protected $table = '__HOOK_PLUGIN__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    //protected $autoWriteTimestamp = true;




	
}