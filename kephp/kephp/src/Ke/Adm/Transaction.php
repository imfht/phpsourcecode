<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm;


use Ke\Utils\Failure;
use Ke\Utils\Status;

/**
 * 数据库事务操作封装
 *
 * 数据库事务的操作，除了几个基本的公共借口外，大部分的方法，都应该是protected或者是private，因为事务都是内部闭环式的操作。
 *
 * 从一个事务到另一个事务之间，直接通过类名实例，和start接口。
 *
 * ```php
 * $purchaseOrderTrans = new Transaction\PurchaseOrder($step, $process);
 * $purchaseOrderTrans->start();
 * if ($purchaseOrderTrans->getStatus()->isSuccess()) {
 *     // 成功时的操作
 * }
 * else {
 *     // 失败时的操作
 * }
 * ```
 *
 * 标准接口说明
 * ```
 * onPrepare      启动前执行的预备操作，主要包括一些数据的依赖验证，数据有误的话，可通过setStatus或者抛出异常。这个阶段，事务的过程为 `Transaction::STEP_PREPARE`
 * onStart        执行事务的操作，这里已经自动开启了相关的数据库事务，数据写入、更新应该在这里开始做。这个阶段，事务的操作步骤为 `Transaction::STEP_START`，这个过程结束，无论成功与否，都直接转为 `Transaction::STEP_COMPLETE`
 * beforeRollBack 事务回滚前的接口
 * afterRollBack  事务回滚后的接口
 * beforeCommit   事务提交前的接口
 * afterCommit    事务提交后的接口
 * ```
 *
 * @package Ke\Adm
 */
abstract class Transaction
{

	/** 初始化阶段 */
	const STEP_INIT = -1;
	/** 预备阶段 */
	const STEP_PREPARE = 0;
	/** 开始阶段 */
	const STEP_START = 1;
	/** 完成阶段 */
	const STEP_COMPLETE = 2;

	/** @var Adapter\Db\PdoMySQL|Adapter\DbAdapter|null */
	private $db = null;

	private $status = null;

	private $step = self::STEP_INIT;

	/**
	 * 取得数据源的访问名
	 *
	 * @return string
	 */
	public function getSource()
	{
		return null;
	}

	/**
	 * 取得数据源的连接实例
	 *
	 * @return Adapter\Db\PdoMySQL|Adapter\DbAdapter
	 */
	public function getDb()
	{
		if (!isset($this->db)) {
			$this->db = Db::getAdapter($this->getSource());
		}
		return $this->db;
	}

	/**
	 * 启动执行事务
	 *
	 * @return $this
	 */
	final public function start()
	{
		// 获取数据源出错的话，不应该算在这个try的过程中。
		$db = $this->getDb();
		try {
			## 0. 预备阶段
			$this->step = self::STEP_PREPARE;
			$this->onPrepare();

			## 1. 启动事务阶段
			## 2. 事务完成
			if (empty($this->status)) {
				$this->step = self::STEP_START;
				$db->startTransaction();
				$this->onStart();
				$this->step = self::STEP_COMPLETE;
			}

			if ($this->getStatus()->isSuccess()) {
				$this->beforeCommit();
				$db->commit();
				$this->afterCommit();
			} else {
				$this->beforeRollBack();
				$db->rollBack();
				$this->afterRollBack();
			}

		} catch (\Throwable $throwable) {
			$this->rollBack($throwable->getMessage());
		}
		return $this;
	}

	protected function onPrepare()
	{
	}

	protected function onStart()
	{
	}

	protected
	function beforeRollBack()
	{
	}

	protected
	function afterRollBack()
	{
	}

	protected
	function beforeCommit()
	{
	}

	protected
	function afterCommit()
	{
	}

	public
	function setStatus(
		$status, string $message = '', array $data = []
	)
	{
		if (!($status instanceof Status)) {
			$status = new Status($status);
		}
		if (!empty($message))
			$status->setMessage($message);
		if (!empty($data))
			$status->setData($data);
		$this->status = $status;
		return $this;
	}

	public
	function getStatus()
	{
		if (!isset($this->status))
			return new Failure('未知的事务状态！');
		return $this->status;
	}

	public
	function rollBack(
		string $message = '', array $data = []
	)
	{
		$this->setStatus(false, $message, $data);
		if ($this->step !== self::STEP_COMPLETE) {
			$this->step = self::STEP_COMPLETE;
			// 启动了事务，才调用数据源回滚
			if ($this->step >= self::STEP_START) {
				$this->beforeRollBack();
				$this->getDb()->rollBack();
				$this->afterRollBack();
			}
		}
		return $this;
	}

	public
	function commit(
		string $message = '', array $data = []
	)
	{
		$this->setStatus(true, $message, $data);
		if ($this->step !== self::STEP_COMPLETE) {
			$this->step = self::STEP_COMPLETE;
			// 启动了事务，才调用数据源提交
			if ($this->step >= self::STEP_START) {
				$this->beforeCommit();
				$this->getDb()->commit();
				$this->afterCommit();
			}
		}
		return $this;
	}
}