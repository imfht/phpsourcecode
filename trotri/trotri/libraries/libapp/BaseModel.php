<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libapp;

use tfc\saf\Log;
use libsrv\AbstractService;

/**
 * BaseModel abstract class file
 * 业务层基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: BaseModel.php 1 2013-05-18 14:58:59Z huan.song $
 * @package libapp
 * @since 1.0
 */
abstract class BaseModel
{
	/**
	 * 调用查询类方法
	 * @param \libsrv\AbstractService $object
	 * @param string $method
	 * @param array $args
	 * @return array
	 */
	public function callFetchMethod(AbstractService $object, $method, array $args = array())
	{
		$data = call_user_func_array(array($object, $method), $args);
		if ($data === false) {
			$errNo = ErrorNo::ERROR_DB_SELECT;
			$errMsg = Lang::_('ERROR_MSG_ERROR_DB_SELECT');
			Log::warning(sprintf(
				'%s callFetchMethod, service "%s", method "%s", args "%s"',
				$errMsg, get_class($object), $method, serialize($args)
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'err_msg' => $errMsg
			);
		}

		$errNo = ErrorNo::SUCCESS_NUM;

		if (empty($data)) {
			$errMsg = Lang::_('ERROR_MSG_ERROR_DB_SELECT_EMPTY');
			return array(
				'err_no' => $errNo,
				'err_msg' => $errMsg,
				'data' => array()
			);
		}

		$errMsg = Lang::_('ERROR_MSG_SUCCESS_SELECT');
		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'data' => $data
		);
	}

	/**
	 * 调用新增类方法
	 * @param \libsrv\AbstractService $object
	 * @param string $method
	 * @param array $params
	 * @param boolean $ignore
	 * @return array
	 */
	public function callCreateMethod(AbstractService $object, $method, array $params = array(), $ignore = false)
	{
		$lastInsertId = $object->$method($params, $ignore);
		$errors = $object->getErrors();
		if (($lastInsertId === false && $errors === array()) || $lastInsertId === 0 || $lastInsertId < 0) {
			$errNo = ErrorNo::ERROR_DB_INSERT;
			$errMsg = Lang::_('ERROR_MSG_ERROR_DB_INSERT');
			Log::warning(sprintf(
				'%s callCreateMethod, service "%s", method "%s", params "%s", ignore "%d"',
				$errMsg, get_class($object), $method, serialize($params), $ignore
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'err_msg' => $errMsg
			);
		}

		if ($lastInsertId === false) {
			$errNo = ErrorNo::ERROR_ARGS_INSERT;
			$errMsg = Lang::_('ERROR_MSG_ERROR_ARGS_INSERT');
			Log::warning(sprintf(
				'%s callCreateMethod, service "%s", method "%s", params "%s", ignore "%d", errors "%s"',
				$errMsg, get_class($object), $method, serialize($params), $ignore, serialize($errors)
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'err_msg' => $errMsg,
				'errors' => $errors
			);
		}

		$errNo = ErrorNo::SUCCESS_NUM;
		$errMsg = Lang::_('ERROR_MSG_SUCCESS_INSERT');
		Log::debug(sprintf(
			'%s callCreateMethod, last insert id "%d", params "%s", ignore "%d"',
			$errMsg, $lastInsertId, serialize($params), $ignore
		), $errNo, __METHOD__);

		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'id' => $lastInsertId
		);
	}

	/**
	 * 调用编辑类方法
	 * @param \libsrv\AbstractService $object
	 * @param string $method
	 * @param integer|array $id
	 * @param array $params
	 * @return array
	 */
	public function callModifyMethod(AbstractService $object, $method, $id, array $params = array())
	{
		$rowCount = $object->$method($id, $params);
		$errors = $object->getErrors();
		if ($rowCount === false) {
			if ($errors === array()) {
				$errNo = ErrorNo::ERROR_DB_UPDATE;
				$errMsg = Lang::_('ERROR_MSG_ERROR_DB_UPDATE');
				Log::warning(sprintf(
					'%s callModifyMethod, service "%s", method "%s", id "%s", params "%s"',
					$errMsg, get_class($object), $method, (is_array($id) ? serialize($id) : $id), serialize($params)
				), $errNo, __METHOD__);
				return array(
					'err_no' => $errNo,
					'err_msg' => $errMsg,
					'id' => $id
				);
			}

			$errNo = ErrorNo::ERROR_ARGS_UPDATE;
			$errMsg = Lang::_('ERROR_MSG_ERROR_ARGS_UPDATE');
			Log::warning(sprintf(
				'%s callModifyMethod, service "%s", method "%s", id "%s", params "%s", errors "%s"',
				$errMsg, get_class($object), $method, (is_array($id) ? serialize($id) : $id), serialize($params), serialize($errors)
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'err_msg' => $errMsg,
				'id' => $id,
				'errors' => $errors
			);
		}

		$errNo = ErrorNo::SUCCESS_NUM;
		$errMsg = ($rowCount > 0) ? Lang::_('ERROR_MSG_SUCCESS_UPDATE') : Lang::_('ERROR_MSG_ERROR_DB_AFFECTS_ZERO');
		Log::debug(sprintf(
			'%s callModifyMethod, service "%s", method "%s", id "%s", rowCount "%d", params "%s"',
			$errMsg, get_class($object), $method, (is_array($id) ? serialize($id) : $id), $rowCount, serialize($params)
		), $errNo, __METHOD__);

		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'id' => $id,
			'row_count' => $rowCount
		);
	}

	/**
	 * 调用删除类方法
	 * @param \libsrv\AbstractService $object
	 * @param string $method
	 * @param integer|array $id
	 * @return array
	 */
	public function callRemoveMethod(AbstractService $object, $method, $id)
	{
		$rowCount = $object->$method($id);
		if ($rowCount === false) {
			$errNo = ErrorNo::ERROR_DB_DELETE;
			$errMsg = Lang::_('ERROR_MSG_ERROR_DB_DELETE');
			Log::warning(sprintf(
				'%s callRemoveMethod, service "%s", method "%s", id "%s"',
				$errMsg, get_class($object), $method, (is_array($id) ? serialize($id) : $id)
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'err_msg' => $errMsg,
				'id' => $id
			);
		}

		$errNo = ErrorNo::SUCCESS_NUM;
		$errMsg = ($rowCount > 0) ? Lang::_('ERROR_MSG_SUCCESS_DELETE') : Lang::_('ERROR_MSG_ERROR_DB_AFFECTS_ZERO');
		Log::debug(sprintf(
			'%s callRemoveMethod, service "%s", method "%s", id "%s", rowCount "%d"',
			$errMsg, get_class($object), $method, (is_array($id) ? serialize($id) : $id), $rowCount
		), $errNo, __METHOD__);

		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'id' => $id,
			'row_count' => $rowCount
		);
	}

	/**
	 * 调用从回收站还原数据类方法
	 * @param \libsrv\AbstractService $object
	 * @param string $method
	 * @param integer|array $id
	 * @return array
	 */
	public function callRestoreMethod(AbstractService $object, $method, $id)
	{
		$rowCount = $object->$method($id);
		if ($rowCount === false) {
			$errNo = ErrorNo::ERROR_ARGS_RESTORE;
			$errMsg = Lang::_('ERROR_MSG_ERROR_DB_RESTORE');
			Log::warning(sprintf(
				'%s callRestoreMethod, service "%s", method "%s", id "%s"',
				$errMsg, get_class($object), $method, (is_array($id) ? serialize($id) : $id)
			), $errNo, __METHOD__);
			return array(
				'err_no' => $errNo,
				'err_msg' => $errMsg,
				'id' => $id
			);
		}

		$errNo = ErrorNo::SUCCESS_NUM;
		$errMsg = ($rowCount > 0) ? Lang::_('ERROR_MSG_SUCCESS_RESTORE') : Lang::_('ERROR_MSG_ERROR_DB_AFFECTS_ZERO');
		Log::debug(sprintf(
			'%s callRestoreMethod, service "%s", method "%s", id "%s", rowCount "%d"',
			$errMsg, get_class($object), $method, (is_array($id) ? serialize($id) : $id), $rowCount
		), $errNo, __METHOD__);

		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'id' => $id,
			'row_count' => $rowCount
		);
	}

	/**
	 * 通过过滤数组，只保留指定的字段名
	 * 如果没有指定要保留的字段名，则通过表的字段过滤
	 * @param array $attributes
	 * @param mixed $columnNames
	 * @return void
	 */
	public function filterAttributes(array &$attributes = array(), $columnNames = null)
	{
		$columnNames = (array) $columnNames;
		foreach ($attributes as $key => $value) {
			if (!in_array($key, $columnNames)) {
				unset($attributes[$key]);
			}
		}
	}
}
