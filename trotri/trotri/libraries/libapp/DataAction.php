<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libapp;

use tfc\ap\Ap;
use tfc\ap\InvalidArgumentException;
use tfc\util\Encoder;
use tfc\saf\Log;

/**
 * DataAction abstract class file
 * DataAction基类，用于Ajax调用和对其他项目提供的接口，需要规范输出数据格式
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DataAction.php 1 2013-04-05 01:08:06Z huan.song $
 * @package libapp
 * @since 1.0
 */
abstract class DataAction extends BaseAction
{
	/**
	 * @var string 缺省的输出数据格式
	 */
	const DEFAULT_DATA_TYPE = 'JSON';

	/**
	 * @var array 项目支持的输出数据格式
	 */
	protected $_dataTypes = array('JSON', 'SERIAL');

	/**
	 * @var string 从Request中获取的od值（output data type）
	 */
	protected $_dataType = self::DEFAULT_DATA_TYPE;

	/**
	 * (non-PHPdoc)
	 * @see \libapp\BaseAction::_init()
	 */
	protected function _init()
	{
		parent::_init();
		$this->_initDataType();
	}

	/**
	 * 初始化项目输出数据格式
	 * @return void
	 * @throws InvalidArgumentException 如果不是可支持的输出数据格式，抛出异常
	 */
	protected function _initDataType()
	{
		// 从RGP中获取‘od’的值（output data type），并验证是否合法
		$dataType = Ap::getRequest()->getTrim('od');
		if ($dataType !== '') {
			$dataType = strtoupper($dataType);
			if (in_array($dataType, $this->_dataTypes)) {
				$this->_dataType = $dataType;
			}
			else {
				throw new InvalidArgumentException(
					'DataAction is unable to determine the output data type of the request.'
				);
			}
		}
	}

	/**
	 * 通过输出数据类型，输出数据
	 * @param mixed $data
	 * @return void
	 * @throws InvalidArgumentException 如果不是可支持的输出数据类型，抛出异常
	 */
	public function display($data)
	{
		// 规范输出格式
		$data = $this->getViewData($data);

		// 数据转Json类型后输出，如果项目不是UTF-8格式，需要先将输出数据转成UTF-8格式
		if ($this->_dataType === 'JSON') {
			if (Ap::getEncoding() !== 'UTF-8') {
				$data = Encoder::getInstance()->convert($data, Ap::getEncoding(), 'UTF-8');
			}

			echo json_encode($data);
			exit;
		}

		// 数据序列化后输出
		if ($this->_dataType === 'SERIAL') {
			echo serialize($data);
			exit;
		}

		throw new InvalidArgumentException(
			'DataAction is unable to determine the output data type of the request.'
		);
	}

	/**
	 * 规范化输出数据的格式
	 * 默认添加的输出内容：log_id (integer)
	 *
	 * 一.参数是字符串：
	 * <pre>
	 * $data = 'trotri';
	 * 返回值：
	 * $ret = array (
	 *     'err_no' => 0,
	 *     'err_msg' => '',
	 *     'data' => 'trotri',
	 *     'log_id' => 2000010
	 * );
	 * </pre>
	 *
	 * 二.参数是数组，但是没有指定err_no和err_msg：
	 * <pre>
	 * $data = array (
	 *     'user_id' => 1,
	 *     'user_name' => 'trotri'
	 * );
	 * 或
	 * $data = array (
	 *     'extra' => '', // 这个值将被丢弃
	 *     'data' => array (
	 *         'user_id' => 1,
	 *         'user_name' => 'trotri'
	 *     )
	 * );
	 * 返回值：
	 * $ret = array (
	 *     'err_no' => 0,
	 *     'err_msg' => '',
	 *     'data' => array (
	 *         'user_id' => 1,
	 *         'user_name' => 'trotri',
	 *     ),
	 *     'log_id' => 2000010
	 * );
	 * </pre>
	 *
	 * 三.参数是数组，并且已经指定err_no和err_msg：
	 * <pre>
	 * $data = array (
	 *     'err_no' => 1001,
	 *     'err_msg' => 'Login Failed',
	 *     'user_id' => 1,
	 *     'user_name' => 'trotri'
	 * );
	 * 或
	 * $data = array (
	 *     'err_no' => 1001,
	 *     'err_msg' => 'Login Failed',
	 *     'extra' => '', // 这个值将被丢弃
	 *     'data' => array (
	 *         'user_id' => 1,
	 *         'user_name' => 'trotri'
	 *     )
	 * );
	 * 返回值：
	 * $ret = array (
	 *     'err_no' => 1001,
	 *     'err_msg' => 'Login Failed',
	 *     'data' => array (
	 *         'user_id' => 1,
	 *         'user_name' => 'trotri'
	 *     ),
	 *     'log_id' => 2000010
	 * );
	 * </pre>
	 * @param mixed $data
	 * @return array
	 */
	public function getViewData($data)
	{
		$errNo = ErrorNo::SUCCESS_NUM;
		$errMsg = '';
		if (is_array($data)) {
			if (isset($data['err_no'])) {
				$errNo = (int) $data['err_no'];
				unset($data['err_no']);
			}

			if (isset($data['err_msg'])) {
				$errMsg = $data['err_msg'];
				unset($data['err_msg']);
			}

			if (isset($data['data'])) {
				$data = $data['data'];
			}
		}

		$ret = array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'data' => $data,
			'log_id' => Log::getId()
		);

		return $ret;
	}
}
