<?php

namespace app\common\model;

/**
 * 公告及消息模型
 */
class Message extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
}
