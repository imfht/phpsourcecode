<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\poll\model;

use libapp\BaseModel;
use tfc\auth\Identity;
use poll\services\Vote AS SrvVote;

/**
 * Vote class file
 * 投票管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Vote.php 1 2013-04-20 17:11:06Z huan.song $
 * @package modules.polls.model
 * @since 1.0
 */
class Vote extends BaseModel
{
	/**
	 * @var \polls\services\Polls 业务处理类
	 */
	protected $_service = null;

	/**
	 * 构造方法：初始化数据库操作类
	 */
	public function __construct()
	{
		$this->_service = new SrvVote();
	}

	/**
	 * 投票
	 * @param string $pollKey
	 * @param string $value
	 * @return array
	 */
	public function addVote($pollKey, $value)
	{
		$ret = $this->_service->addVote($pollKey, $value, Identity::getUserId(), Identity::getRankId());
		return $ret;
	}

}
