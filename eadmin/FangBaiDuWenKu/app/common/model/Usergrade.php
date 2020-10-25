<?php

namespace app\common\model;

/**
 * 权限组模型
 */
class Usergrade extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
}
