<?php

namespace app\common\model;

/**
 * 导航模型
 */
class Nav extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
}
