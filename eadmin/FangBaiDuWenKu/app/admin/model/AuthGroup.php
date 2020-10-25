<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

/**
 * 权限组模型
 */
class AuthGroup extends AdminBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];

}
