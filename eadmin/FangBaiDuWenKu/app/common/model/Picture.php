<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 图片模型
 */
class Picture extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
}
