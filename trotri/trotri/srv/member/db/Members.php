<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace member\db;

use tdo\AbstractDb;
use member\library\Constant;
use member\library\TableNames;

/**
 * Members class file
 * 业务层：数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Members.php 1 2014-11-27 17:10:30Z Code Generator $
 * @package member.db
 * @since 1.0
 */
class Members extends AbstractDb
{
	/**
	 * @var string 数据库配置名
	 */
	protected $_clusterName = Constant::DB_CLUSTER;

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
		$commandBuilder = $this->getCommandBuilder();
		$membersTblName = $this->getTblprefix() . TableNames::getMembers();
		$portalTblName = $this->getTblprefix() . TableNames::getPortal();
		$sql = 'SELECT ' . $option . ' `m`.`member_id`, `p`.`login_name`, `p`.`member_name`, `p`.`member_mail`, `p`.`member_phone`, `m`.`type_id`, `m`.`rank_id`, `m`.`experience`, `m`.`balance`, `m`.`balance_freeze`, `m`.`points`, `m`.`points_freeze`, `m`.`consum`, `m`.`orders`, `m`.`description`, `m`.`dt_last_rerank`, `m`.`dt_created` FROM `' . $membersTblName . '` AS `m` LEFT JOIN `' . $portalTblName . '` AS `p` ON `m`.`member_id` = `p`.`member_id`';

		$condition = '`p`.`trash` = ' . $commandBuilder::PLACE_HOLDERS;
		$attributes = array('trash' => 'n');

		if (isset($params['login_name'])) {
			$loginName = trim($params['login_name']);
			if ($loginName !== '') {
				$condition .= ' AND `p`.`login_name` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_name'] = $loginName;
			}
		}

		if (isset($params['login_type'])) {
			$loginType = trim($params['login_type']);
			if ($loginType !== '') {
				$condition .= ' AND `p`.`login_type` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['login_type'] = $loginType;
			}
		}

		if (isset($params['member_name'])) {
			$memberName = trim($params['member_name']);
			$condition .= ' AND `p`.`member_name` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_name'] = $memberName;
		}

		if (isset($params['member_mail'])) {
			$memberMail = trim($params['member_mail']);
			$condition .= ' AND `p`.`member_mail` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_mail'] = $memberMail;
		}

		if (isset($params['member_phone'])) {
			$memberPhone = trim($params['member_phone']);
			$condition .= ' AND `p`.`member_phone` = ' . $commandBuilder::PLACE_HOLDERS;
			$attributes['member_phone'] = $memberPhone;
		}

		if (isset($params['type_id'])) {
			$typeId = (int) $params['type_id'];
			if ($typeId > 0) {
				$condition .= ' AND `m`.`type_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['type_id'] = $typeId;
			}
		}

		if (isset($params['rank_id'])) {
			$rankId = (int) $params['rank_id'];
			if ($rankId > 0) {
				$condition .= ' AND `m`.`rank_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['rank_id'] = $rankId;
			}
		}

		if (isset($params['member_id'])) {
			$memberId = (int) $params['member_id'];
			if ($memberId > 0) {
				$condition .= ' AND `m`.`member_id` = ' . $commandBuilder::PLACE_HOLDERS;
				$attributes['member_id'] = $memberId;
			}
		}

		$sql = $commandBuilder->applyCondition($sql, $condition);
		$sql = $commandBuilder->applyOrder($sql, $order);
		$sql = $commandBuilder->applyLimit($sql, $limit, $offset);

		if ($option === 'SQL_CALC_FOUND_ROWS') {
			$ret = $this->fetchAllNoCache($sql, $attributes);
			if (is_array($ret)) {
				$ret['attributes'] = $attributes;
				$ret['order']      = $order;
				$ret['limit']      = $limit;
				$ret['offset']     = $offset;
			}
		}
		else {
			$ret = $this->fetchAll($sql, $attributes);
		}

		return $ret;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $memberId
	 * @return array
	 */
	public function findByPk($memberId)
	{
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getMembers();
		$sql = 'SELECT `member_id`, `login_name`, `p_password`, `p_salt`, `type_id`, `rank_id`, `experience`, `balance`, `balance_freeze`, `points`, `points_freeze`, `consum`, `orders`, `description`, `dt_last_rerank`, `dt_created` FROM `' . $tableName . '` WHERE `member_id` = ?';
		return $this->fetchAssoc($sql, $memberId);
	}

	/**
	 * 通过主键，编辑支付密码
	 * @param integer $memberId
	 * @param string $pPwd
	 * @param string $pSalt
	 * @return integer
	 */
	public function modifyPPwd($memberId, $pPwd, $pSalt)
	{
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		if (($pPwd = trim($pPwd)) === '') {
			return false;
		}

		if (($pSalt = trim($pSalt)) === '') {
			return false;
		}

		$attributes = array(
			'p_password' => $pPwd,
			'p_salt' => $pSalt,
			'member_id' => $memberId
		);

		$tableName = $this->getTblprefix() . TableNames::getMembers();
		$sql = 'UPDATE `' . $tableName . '` SET `p_password` = ?, `p_salt` = ? WHERE `member_id` = ?';
		$rowCount = $this->update($sql, $attributes);
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
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		if (($typeId = (int) $typeId) <= 0) {
			return false;
		}

		$attributes = array(
			'type_id' => $typeId,
			'member_id' => $memberId
		);

		$tableName = $this->getTblprefix() . TableNames::getMembers();
		$sql = 'UPDATE `' . $tableName . '` SET `type_id` = ? WHERE `member_id` = ?';
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
	}

	/**
	 * 通过主键，编辑会员成长度ID
	 * @param integer $memberId
	 * @param integer $rankId
	 * @return integer
	 */
	public function modifyRankId($memberId, $rankId)
	{
		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		if (($rankId = (int) $rankId) <= 0) {
			return false;
		}

		$attributes = array(
			'rank_id' => $rankId,
			'dt_last_rerank' => date('Y-m-d H:i:s'),
			'member_id' => $memberId
		);

		$tableName = $this->getTblprefix() . TableNames::getMembers();
		$sql = 'UPDATE `' . $tableName . '` SET `rank_id` = ?, `dt_last_rerank` = ? WHERE `member_id` = ?';
		$rowCount = $this->update($sql, $attributes);
		return $rowCount;
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
		if ($opType !== 'increase' && $opType !== 'reduce') {
			return false;
		}

		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		if (($experience = (int) $experience) <= 0) {
			return false;
		}

		if (($source = trim($source)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getMembers();
		$logTblName = $this->getTblprefix() . TableNames::getExperienceLogs();

		$sql = 'SELECT `experience` FROM `' . $tableName . '` WHERE `member_id` = ?';
		$beforeExperience = $this->fetchColumn($sql, $memberId);
		if ($beforeExperience === false) {
			return false;
		}

		$beforeExperience = (int) $beforeExperience;
		$afterExperience = ($opType === 'increase') ? ($beforeExperience + $experience) : ($beforeExperience - $experience);
		if ($afterExperience < 0) {
			return false;
		}

		$attributes = array(
			'member_id' => $memberId,
			'op_type' => $opType,
			'before_experience' => $beforeExperience,
			'after_experience' => $afterExperience,
			'experience' => $experience,
			'source' => $source,
			'remarks' => $remarks,
			'creator_id' => (int) $creatorId,
			'dt_created' => date('Y-m-d H:i:s'),
		);

		$commands = array(
			array(
				'sql' => 'UPDATE `' . $tableName . '` SET `experience` = ? WHERE `member_id` = ?',
				'params' => array(
					'experience' => $afterExperience,
					'member_id' => $memberId
				)
			),
			array(
				'sql' => $this->getCommandBuilder()->createInsert($logTblName, array_keys($attributes)),
				'params' => $attributes
			),
		);

		return $this->doTransaction($commands);
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
		if ($opType !== 'increase' && $opType !== 'reduce' && $opType !== 'freeze' && $opType !== 'unfreeze' && $opType !== 'reduce_freeze') {
			return false;
		}

		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		if (($balance = (float) $balance) <= 0) {
			return false;
		}

		if (($source = trim($source)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getMembers();
		$logTblName = $this->getTblprefix() . TableNames::getBalanceLogs();

		$sql = 'SELECT `balance`, `balance_freeze` FROM `' . $tableName . '` WHERE `member_id` = ?';
		$row = $this->fetch($sql, $memberId);
		if (!$row || !is_array($row) || !isset($row['balance'])) {
			return false;
		}

		$beforeBalance = (float) $row['balance'];
		$beforeFreezeBalance = (float) $row['balance_freeze'];

		$freezeBalance = 0;
		if ($opType === 'freeze' || $opType === 'unfreeze' || $opType === 'reduce_freeze') {
			$freezeBalance = $balance;
			$balance = 0;
		}

		$afterFreezeBalance = ($opType === 'freeze') ? ($beforeFreezeBalance + $freezeBalance) : ($beforeFreezeBalance - $freezeBalance);
		switch (true) {
			case $opType === 'increase':
				$afterBalance = $beforeBalance + $balance;
				break;
			case $opType === 'reduce':
				$afterBalance = $beforeBalance - $balance;
				break;
			case $opType === 'freeze':
				$afterBalance = $beforeBalance - $freezeBalance;
				break;
			case $opType === 'unfreeze':
				$afterBalance = $beforeBalance + $freezeBalance;
				break;
			case $opType === 'reduce_freeze':
			default:
				$afterBalance = $beforeBalance;
				break;
		}

		if ($afterBalance < 0 || $afterBalance < 0) {
			return false;
		}

		$attributes = array(
			'member_id' => $memberId,
			'op_type' => $opType,
			'before_balance' => $beforeBalance,
			'after_balance' => $afterBalance,
			'balance' => $balance,
			'before_freeze_balance' => $beforeFreezeBalance,
			'after_freeze_balance' => $afterFreezeBalance,
			'freeze_balance' => $freezeBalance,
			'source' => $source,
			'remarks' => $remarks,
			'creator_id' => (int) $creatorId,
			'dt_created' => date('Y-m-d H:i:s'),
		);

		$commands = array(
			array(
				'sql' => 'UPDATE `' . $tableName . '` SET `balance` = ?, `balance_freeze` = ? WHERE `member_id` = ?',
				'params' => array(
					'balance' => $afterBalance,
					'balance_freeze' => $afterFreezeBalance,
					'member_id' => $memberId
				)
			),
			array(
				'sql' => $this->getCommandBuilder()->createInsert($logTblName, array_keys($attributes)),
				'params' => $attributes
			),
		);

		return $this->doTransaction($commands);
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
		if ($opType !== 'increase' && $opType !== 'reduce' && $opType !== 'freeze' && $opType !== 'unfreeze' && $opType !== 'reduce_freeze') {
			return false;
		}

		if (($memberId = (int) $memberId) <= 0) {
			return false;
		}

		if (($points = (int) $points) <= 0) {
			return false;
		}

		if (($source = trim($source)) === '') {
			return false;
		}

		$tableName = $this->getTblprefix() . TableNames::getMembers();
		$logTblName = $this->getTblprefix() . TableNames::getPointsLogs();

		$sql = 'SELECT `points`, `points_freeze` FROM `' . $tableName . '` WHERE `member_id` = ?';
		$row = $this->fetch($sql, $memberId);
		if (!$row || !is_array($row) || !isset($row['points'])) {
			return false;
		}

		$beforePoints = (int) $row['points'];
		$beforeFreezePoints = (int) $row['points_freeze'];

		$freezePoints = 0;
		if ($opType === 'freeze' || $opType === 'unfreeze' || $opType === 'reduce_freeze') {
			$freezePoints = $points;
			$points = 0;
		}

		$afterFreezePoints = ($opType === 'freeze') ? ($beforeFreezePoints + $freezePoints) : ($beforeFreezePoints - $freezePoints);
		switch (true) {
			case $opType === 'increase':
				$afterPoints = $beforePoints + $points;
				break;
			case $opType === 'reduce':
				$afterPoints = $beforePoints - $points;
				break;
			case $opType === 'freeze':
				$afterPoints = $beforePoints - $freezePoints;
				break;
			case $opType === 'unfreeze':
				$afterPoints = $beforePoints + $freezePoints;
				break;
			case $opType === 'reduce_freeze':
			default:
				$afterPoints = $beforePoints;
				break;
		}

		if ($afterPoints < 0 || $afterFreezePoints < 0) {
			return false;
		}

		$attributes = array(
			'member_id' => $memberId,
			'op_type' => $opType,
			'before_points' => $beforePoints,
			'after_points' => $afterPoints,
			'points' => $points,
			'before_freeze_points' => $beforeFreezePoints,
			'after_freeze_points' => $afterFreezePoints,
			'freeze_points' => $freezePoints,
			'source' => $source,
			'remarks' => $remarks,
			'creator_id' => (int) $creatorId,
			'dt_created' => date('Y-m-d H:i:s'),
		);

		$commands = array(
			array(
				'sql' => 'UPDATE `' . $tableName . '` SET `points` = ?, `points_freeze` = ? WHERE `member_id` = ?',
				'params' => array(
					'points' => $afterPoints,
					'points_freeze' => $afterFreezePoints,
					'member_id' => $memberId
				)
			),
			array(
				'sql' => $this->getCommandBuilder()->createInsert($logTblName, array_keys($attributes)),
				'params' => $attributes
			),
		);

		return $this->doTransaction($commands);
	}

}
