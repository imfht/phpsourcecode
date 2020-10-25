<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\model;

use libapp\BaseModel;
use tfc\saf\Text;
use libapp\ErrorNo;
use member\services\Social AS SrvSocial;

/**
 * Social class file
 * 会员找回密码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Social.php 1 2014-08-08 14:05:27Z Code Generator $
 * @package modules.member.model
 * @since 1.0
 */
class Social extends BaseModel
{
	/**
	 * @var srv\srvname\services\classname 业务处理类
	 */
	protected $_service = null;

	/**
	 * 构造方法：初始化数据库操作类
	 */
	public function __construct()
	{
		$this->_service = new SrvSocial();
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $value
	 * @return array
	 */
	public function findByPk($value)
	{
		$row = $this->_service->findByPk($value);
		return $row;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $id
	 * @param array $params
	 * @return array
	 */
	public function modifyByPk($id, array $params = array())
	{
		$ret = $this->callModifyMethod($this->_service, 'modifyByPk', $id, $params);
		if ($ret['err_no'] === ErrorNo::SUCCESS_NUM) {
			$ret['err_msg'] = Text::_('MOD_MEMBER_SOCIAL_MODIFY_SUCCESS_HINT');
		}

		return $ret;
	}

}
