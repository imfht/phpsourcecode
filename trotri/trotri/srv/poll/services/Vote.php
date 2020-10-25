<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace poll\services;

use libsrv\AbstractService;
use tfc\ap\Ap;
use libsrv\Clean;
use libsrv\Service;
use libsrv;

/**
 * Vote class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Vote.php 1 2014-12-05 17:47:10Z Code Generator $
 * @package poll.services
 * @since 1.0
 */
class Vote extends AbstractService
{
	/**
	 * 通过投票ID和会员ID，获取会员投票日志
	 * @param integer $pollId
	 * @param integer $memberId
	 * @return array
	 */
	public function getMemberLogs($pollId, $memberId)
	{
		$row = $this->getDb()->getMemberLogs($pollId, $memberId);
		return $row;
	}

	/**
	 * 通过投票ID和游客IP，获取游客投票日志
	 * @param integer $pollId
	 * @param integer $visitorIp
	 * @return array
	 */
	public function getVisitorLogs($pollId, $visitorIp = null)
	{
		if ($visitorIp === null) {
			$visitorIp = Clean::ip2long(Ap::getRequest()->getClientIp());
		}

		$row = $this->getDb()->getVisitorLogs($pollId, $visitorIp);
		return $row;
	}

	/**
	 * 投票-支持会员或游客、支持单选或多选
	 * @param string $pollKey
	 * @param string $value
	 * @param integer $memberId
	 * @param integer $rankId
	 * @return array
	 */
	public function addVote($pollKey, $value, $memberId, $rankId)
	{
		$row = Service::getInstance('Polls', 'poll')->findByPollKey($pollKey, false);
		if (!$row || !is_array($row) || !isset($row['poll_id']) || !isset($row['is_published'])) {
			$errNo = DataVote::ERROR_FAILED;

			return array(
				'err_no' => $errNo,
				'err_msg' => DataVote::getErrMsgByErrNo($errNo),
			);
		}

		if (!$row['is_published']) {
			$errNo = DataVote::ERROR_DT_PUBLISH_DOWN_WRONG;

			return array(
				'err_no' => $errNo,
				'err_msg' => DataVote::getErrMsgByErrNo($errNo),
			);
		}

		$nowTime = date('Y-m-d H:i:s');
		if ($nowTime < $row['dt_publish_up']) {
			$errNo = DataVote::ERROR_DT_PUBLISH_UP_WRONG;

			return array(
				'err_no' => $errNo,
				'err_msg' => DataVote::getErrMsgByErrNo($errNo, $row['dt_publish_up']),
			);
		}

		if ($row['dt_publish_down'] !== '0000-00-00 00:00:00' && $nowTime > $row['dt_publish_down']) {
			$errNo = DataVote::ERROR_DT_PUBLISH_DOWN_WRONG;

			return array(
				'err_no' => $errNo,
				'err_msg' => DataVote::getErrMsgByErrNo($errNo),
			);
		}

		$checked = array();
		foreach (explode(',', $value) as $_v) {
			if (($_v = (int) $_v) > 0 && !in_array($_v, $checked)) {
				$checked[] = $_v;
			}
		}

		$memberId   = (int) $memberId;
		$rankId     = (int) $rankId;
		$pollId     = (int) $row['poll_id'];
		$optIds     = array();
		$visitorIp  = Clean::ip2long(Ap::getRequest()->getClientIp());
		$allowUnregistered = $row['allow_unregistered'];
		$isMultiple = $row['is_multiple'];
		$maxChoices = (int) $row['max_choices'];
		$joinType   = isset($row['join_type'])  ? $row['join_type']          : '';
		$interval   = isset($row['interval'])   ? (int) $row['interval']     : 0;
		$mRankIds   = isset($row['m_rank_ids']) ? (array) $row['m_rank_ids'] : array();

		if ($allowUnregistered) {
			$memberId = 0;
			$rankId = 0;
		}
		else {
			if ($memberId <= 0) {
				$errNo = DataVote::ERROR_ALLOW_UNREGISTERED_WRONG;

				return array(
					'err_no' => $errNo,
					'err_msg' => DataVote::getErrMsgByErrNo($errNo),
				);
			}

			if ($mRankIds !== array() && !in_array($rankId, $mRankIds)) {
				$errNo = DataVote::ERROR_M_RANK_ID_WRONG;

				return array(
					'err_no' => $errNo,
					'err_msg' => DataVote::getErrMsgByErrNo($errNo),
				);
			}
		}

		if ($checked === array()) {
			$errNo = DataVote::ERROR_POLLOPTIONS_EMPTY;

			return array(
				'err_no' => $errNo,
				'err_msg' => DataVote::getErrMsgByErrNo($errNo),
			);
		}

		if ($isMultiple) {
			if ($maxChoices > 0 && count($checked) > $maxChoices) {
				$errNo = DataVote::ERROR_POLLOPTIONS_WRONG;

				return array(
					'err_no' => $errNo,
					'err_msg' => DataVote::getErrMsgByErrNo($errNo, $maxChoices),
				);
			}
		}

		$options = Service::getInstance('Polloptions', 'poll')->findAllByPollId($pollId);
		foreach ($options as $row) {
			$optId = isset($row['option_id']) ? (int) $row['option_id'] : 0;
			if (in_array($optId, $checked)) {
				$optIds[] = $optId;
			}
		}

		if ($optIds === array()) {
			$errNo = DataVote::ERROR_POLLOPTIONS_NOT_EXISTS;

			return array(
				'err_no' => $errNo,
				'err_msg' => DataVote::getErrMsgByErrNo($errNo),
			);
		}

		if (!$isMultiple) {
			$optIds = array_shift($optIds);
		}

		$row = array();
		if ($memberId > 0) {
			$row = $this->getMemberLogs($pollId, $memberId);
		}
		else {
			$row = $this->getVisitorLogs($pollId, $visitorIp);
		}

		if ($row && is_array($row) && isset($row['ts_last_modified'])) {
			if (($tsLastModified = (int) $row['ts_last_modified']) > 0) {
				$errNo = DataVote::SUCCESS_NUM;

				switch ($joinType) {
					case DataPolls::JOIN_TYPE_FOREVER:
						$errNo = DataVote::ERROR_JOIN_TYPE_FOREVER_WRONG;
						break;
					case DataPolls::JOIN_TYPE_YEAR:
						if (date('Y', $tsLastModified) === date('Y')) { $errNo = DataVote::ERROR_JOIN_TYPE_YEAR_WRONG; }
						break;
					case DataPolls::JOIN_TYPE_MONTH:
						if (date('Ym', $tsLastModified) === date('Ym')) { $errNo = DataVote::ERROR_JOIN_TYPE_MONTH_WRONG; }
						break;
					case DataPolls::JOIN_TYPE_DAY:
						if (date('Ymd', $tsLastModified) === date('Ymd')) { $errNo = DataVote::ERROR_JOIN_TYPE_DAY_WRONG; }
						break;
					case DataPolls::JOIN_TYPE_HOUR:
						if (date('YmdH', $tsLastModified) === date('YmdH')) { $errNo = DataVote::ERROR_JOIN_TYPE_HOUR_WRONG; }
						break;
					case DataPolls::JOIN_TYPE_INTERVAL:
					default:
						if ((time() - $tsLastModified) <= $interval) { $errNo = DataVote::ERROR_JOIN_TYPE_INTERVAL_WRONG; }
				}

				if ($errNo !== DataVote::SUCCESS_NUM) {
					return array(
						'err_no' => $errNo,
						'err_msg' => DataVote::getErrMsgByErrNo($errNo, $interval),
					);
				}
			}
		}

		if (!$this->getDb()->addVote($pollId, $optIds, $visitorIp, $memberId)) {
			$errNo = DataVote::ERROR_FAILED;

			return array(
				'err_no' => $errNo,
				'err_msg' => DataVote::getErrMsgByErrNo($errNo),
			);
		}

		$errNo = DataVote::SUCCESS_NUM;

		return array(
			'err_no' => $errNo,
			'err_msg' => DataVote::getErrMsgByErrNo($errNo),
		);
	}
}
