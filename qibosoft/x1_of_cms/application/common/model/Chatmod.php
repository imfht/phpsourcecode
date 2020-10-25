<?php

namespace app\common\model;
use think\Model;


//群聊模块
class Chatmod extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__CHATMOD__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	protected $pk = 'id';	
}