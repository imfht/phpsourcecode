<?php
namespace plugins\comment\model;
use think\Model;


//评论内容表
class Content extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__COMMENT_CONTENT__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
	//主键不是ID,要单独指定
	//protected $pk = 'id';
    
    
}