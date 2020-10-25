<?php

namespace app\common\model;

/**
 * 悬赏模型
 */
class Docxs extends ModelBase
{
	protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
}
