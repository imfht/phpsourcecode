<?php

namespace plugins\marketing\model;
use think\Model;


//积分日志
class Moneylog extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__MONEYLOG__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	protected $pk = 'id';

	
}