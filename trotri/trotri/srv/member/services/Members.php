<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\services;

use libsrv\AbstractService;
use tfc\util\String;
use libsrv\FormProcessor;
use libsrv\Service;
use member\library\Constant;

/**
 * Members class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Members.php 1 2014-11-27 17:10:30Z Code Generator $
 * @package member.services
 * @since 1.0
 */
class Members extends AbstractService
{
	/**
	 * 查询多条记录
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @param string $option
	 * @return array
	 */
	public function findAll(array $params = array(), $order = '', $limit = 0, $offset = 0, $option = '')
	{
		$limit = min(max((int) $limit, 1), Constant::FIND_MAX_LIMIT);
		$offset = max((int) $offset, 0);

		$rows = $this->getDb()->findAll($params, $order, $limit, $offset, $option);
		return $rows;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findByPk($memberId)
	{
		$row = $this->getDb()->findByPk($memberId);
		return $row;
	}

	/**
	 * 通过主键，编辑支付密码
	 * @param integer|array $value
	 * @param array $params
	 * @return integer
	 */
	public function modifyByPk($value, array $params = array())
	{
		if (isset($params['type_id'])) {
			return $this->modifyTypeId($value, $params['type_id']);
		}

		if (isset($params['p_password']) && isset($params['p_repassword'])) {
			return $this->modifyPPwd($value, $params['p_password'], $params['p_repassword']);
		}

		return false;
	}

	/**
	 * 通过主键，编辑支付密码
	 * @param integer $memberId
	 * @param string $pPwd
	 * @param string $pRepwd
	 * @return integer
	 */
	public function modifyPPwd($memberId, $pPwd, $pRepwd)
	{
		$formProcessor = $this->getFormProcessor();
		if (!$formProcessor->run(FormProcessor::OP_UPDATE, array('p_password' => $pPwd, 'p_repassword' => $pRepwd), $memberId)) {
			return false;
		}

		$rowCount = $this->getDb()->modifyPPwd($formProcessor->id, $formProcessor->p_password, $formProcessor->p_salt);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑会员类型ID
	 * @param integer $memberId
	 * @param integer $typeId
	 * @return integer
	 */
	public function modifyTypeId($memberId, $typeId)
	{
		$row = Service::getInstance('Types', 'member')->findByPk($typeId);
		if ($row && is_array($row) && isset($row['type_id'])) {
			$rowCount = $this->getDb()->modifyTypeId($memberId, $typeId);
			return $rowCount;
		}

		return false;
	}

	/**
	 * 通过主键，编辑会员成长度ID
	 * @param integer $memberId
	 * @param integer $rankId
	 * @return integer
	 */
	public function modifyRankId($memberId, $rankId)
	{
		$row = Service::getInstance('Ranks', 'member')->findByPk($rankId);
		if ($row && is_array($row) && isset($row['rank_id'])) {
			$rowCount = $this->getDb()->modifyRankId($memberId, $rankId);
			return $rowCount;
		}

		return false;
	}

	/**
	 * 操作成长值
	 * @param string $opType increase：增加、reduce：扣除
	 * @param integer $memberId
	 * @param integer $experience
	 * @param string $source
	 * @param string $remarks
	 * @param integer $creatorId
	 * @return boolean
	 */
	public function opExperience($opType, $memberId, $experience, $source, $remarks, $creatorId)
	{
		return $this->getDb()->opExperience($opType, $memberId, $experience, $source, $remarks, $creatorId);
	}

	/**
	 * 操作预存款金额
	 * @param string $opType increase：增加、reduce：扣除、freeze：冻结、unfreeze：解冻、reduce_freeze：扣除冻结金额
	 * @param integer $memberId
	 * @param float $balance
	 * @param string $source
	 * @param string $remarks
	 * @param integer $creatorId
	 * @return boolean
	 */
	public function opBalance($opType, $memberId, $balance, $source, $remarks, $creatorId)
	{
		return $this->getDb()->opBalance($opType, $memberId, $balance, $source, $remarks, $creatorId);
	}

	/**
	 * 操作积分
	 * @param string $opType increase：增加、reduce：扣除、freeze：冻结、unfreeze：解冻、reduce_freeze：扣除冻结积分
	 * @param integer $memberId
	 * @param integer $points
	 * @param string $source
	 * @param string $remarks
	 * @param integer $creatorId
	 * @return boolean
	 */
	public function opPoints($opType, $memberId, $points, $source, $remarks, $creatorId)
	{
		return $this->getDb()->opPoints($opType, $memberId, $points, $source, $remarks, $creatorId);
	}

	/**
	 * 通过“主键ID”，获取“支付密码”
	 * @param integer $memberId
	 * @return string
	 */
	public function getPPasswordByMemberId($memberId)
	{
		$value = $this->getByPk('p_password', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“支付密码-随机附加混淆码”
	 * @param integer $memberId
	 * @return string
	 */
	public function getPSaltByMemberId($memberId)
	{
		$value = $this->getByPk('p_salt', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“会员类型”
	 * @param integer $memberId
	 * @return string
	 */
	public function getTypeIdByMemberId($memberId)
	{
		$value = $this->getByPk('type_id', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“会员成长度”
	 * @param integer $memberId
	 * @return string
	 */
	public function getRankIdByMemberId($memberId)
	{
		$value = $this->getByPk('rank_id', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“成长值”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getExperienceByMemberId($memberId)
	{
		$value = $this->getByPk('experience', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“预存款金额”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getBalanceByMemberId($memberId)
	{
		$value = $this->getByPk('balance', $memberId);
		return $value ? (float) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“预存款冻结金额”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getBalanceFreezeByMemberId($memberId)
	{
		$value = $this->getByPk('balance_freeze', $memberId);
		return $value ? (float) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“积分”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getPointsByMemberId($memberId)
	{
		$value = $this->getByPk('points', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“冻结积分”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getPointsFreezeByMemberId($memberId)
	{
		$value = $this->getByPk('points_freeze', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“消费总额”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getConsumByMemberId($memberId)
	{
		$value = $this->getByPk('consum', $memberId);
		return $value ? (float) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“订单总数”
	 * @param integer $memberId
	 * @return integer
	 */
	public function getOrdersByMemberId($memberId)
	{
		$value = $this->getByPk('orders', $memberId);
		return $value ? (int) $value : 0;
	}

	/**
	 * 通过“主键ID”，获取“描述”
	 * @param integer $memberId
	 * @return string
	 */
	public function getDescriptionByMemberId($memberId)
	{
		$value = $this->getByPk('description', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“上次更新成长度时间”
	 * @param integer $memberId
	 * @return string
	 */
	public function getDtLastRerankByMemberId($memberId)
	{
		$value = $this->getByPk('dt_last_rerank', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 通过“主键ID”，获取“创建时间”
	 * @param integer $memberId
	 * @return string
	 */
	public function getDtCreatedByMemberId($memberId)
	{
		$value = $this->getByPk('dt_created', $memberId);
		return $value ? $value : '';
	}

	/**
	 * 获取支付随机附加混淆码
	 * @return string
	 */
	public function getSalt()
	{
		return String::randStr(6);
	}

	/**
	 * 加密用户支付密码
	 * @param string $pwd
	 * @param string $salt
	 * @return string
	 */
	public function encrypt($pwd, $salt = '')
	{
		return md5($salt . substr(md5($pwd), 3));
	}

}
