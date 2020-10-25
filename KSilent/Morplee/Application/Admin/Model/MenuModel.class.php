<?php

namespace Admin\Model;
use Think\Model;

/**
 * 菜单控制
 */

class MenuModel extends Model {

	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = "u_";

	/* 验证规则 */
    protected $_validate = array(
        array('menuName','require','标题必须填写'), 
        array('menuUrl','require','链接必须填写'), 
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('title', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('status', '1', self::MODEL_INSERT),
        array('addTime', NOW_TIME, self::MODEL_INSERT),
    );

}