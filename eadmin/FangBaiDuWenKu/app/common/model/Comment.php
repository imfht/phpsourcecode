<?php

namespace app\common\model;

/**
 * 评论模型
 */
class Comment extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
}
