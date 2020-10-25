<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace library;

use libapp;
use tfc\ap\HttpCookie;
use tfc\mvc\Mvc;
use tfc\saf\Log;
use libsrv\Service;
use libsrv\Clean;
use libapp\Lang;

/**
 * BaseModel abstract class file
 * 模型基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: BaseModel.php 1 2013-05-18 14:58:59Z huan.song $
 * @package library
 * @since 1.0
 */
abstract class BaseModel extends libapp\BaseModel
{
	/**
	 * @var srv\srvname\services\Types 业务处理类
	 */
	protected $_service = null;

	/**
	 * @var string 业务名
	 */
	protected $_srvName = '';

	/**
	 * @var string 模型类名
	 */
	protected $_className = '';

	/**
	 * @var string 存放最后一次访问的列表页链接的Cookie名
	 */
	const LLU_COOKIE_NAME = 'last_list_url';

	/**
	 * @var integer 存放在Cookie中的列表页链接数量
	 */
	const LLU_COOKIE_COUNT = 4;

	/**
	 * @var string 缺省的列表页方法名
	 */
	const DEFAULT_ACT_NAME_LIST = 'index';

	/**
	 * @var string 缺省的详情页方法名
	 */
	const DEFAULT_ACT_NAME_VIEW = 'view';

	/**
	 * @var string 缺省的新增数据方法名
	 */
	const DEFAULT_ACT_NAME_CREATE = 'create';

	/**
	 * @var string 缺省的编辑数据方法名
	 */
	const DEFAULT_ACT_NAME_MODIFY = 'modify';

	/**
	 * @var string 缺省的删除数据方法名
	 */
	const DEFAULT_ACT_NAME_REMOVE = 'remove';

	/**
	 * @var 模板解析类、URL管理类、页面辅助类、模型名、控制器名、方法名、缺省的列表页方法名、缺省的详情页方法名、缺省的新增数据方法名、缺省的编辑数据方法名
	 */
	public
		$view,
		$urlManager,
		$html,
		$module,
		$controller,
		$action,
		$actNameList = self::DEFAULT_ACT_NAME_LIST,
		$actNameView = self::DEFAULT_ACT_NAME_VIEW,
		$actNameCreate = self::DEFAULT_ACT_NAME_CREATE,
		$actNameModify = self::DEFAULT_ACT_NAME_MODIFY,
		$actNameRemove = self::DEFAULT_ACT_NAME_REMOVE;

	/**
	 * 构造方法，初始化模板解析类、URL管理类、页面辅助类、模型名、控制器名、方法名、缺省的列表页方法名、缺省的详情页方法名、缺省的新增数据方法名、缺省的编辑数据方法名
	 */
	public function __construct()
	{
		$this->view = Mvc::getView();
		$this->urlManager = $this->view->getUrlManager();
		$this->html = $this->view->getHtml();
		$this->module = Mvc::$module;
		$this->controller = Mvc::$controller;
		$this->action = Mvc::$action;

		list(, $moduleName, , $className) = explode('\\', get_class($this));
		if ($this->_srvName === '') {
			$this->_srvName = $moduleName;
		}

		if ($this->_className === '') {
			$this->_className = $className;
		}

		$this->_init();
	}

	/**
	 * 子类构造方法：子类调用此方法作为构造方法，避免重写父类构造方法
	 */
	protected function _init()
	{
	}

	/**
	 * 查询数据列表
	 * @param array $params
	 * @param string $order
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function search(array $params = array(), $order = '', $limit = null, $offset = null)
	{
		if ($limit === null) {
			$limit = PageHelper::getListRows();
		}

		if ($offset === null) {
			$offset = PageHelper::getFirstRow();
		}

		$ret = $this->callFetchMethod($this->getService(), 'findAll', array($params, $order, $limit, $offset, 'SQL_CALC_FOUND_ROWS'));
		if ($ret['err_no'] !== ErrorNo::SUCCESS_NUM) {
			return $ret;
		}

		$data = $ret['data']['rows'];
		unset($ret['data']['rows']);

		$ret['paginator'] = $ret['data'];
		$ret['data'] = $data;

		return $ret;
	}

	/**
	 * 通过主键，查询一条记录
	 * @param integer $value
	 * @return array
	 */
	public function findByPk($value)
	{
		$ret = $this->callFetchMethod($this->getService(), 'findByPk', array($value));
		return $ret;
	}

	/**
	 * 通过主键，获取某个列的值
	 * @param string $columnName
	 * @param integer $amcaId
	 * @return mixed
	 */
	public function getByPk($columnName, $amcaId)
	{
		return $this->getService()->getByPk($columnName, $amcaId);
	}

	/**
	 * 新增一条记录
	 * @param array $params
	 * @param boolean $ignore
	 * @return array
	 */
	public function create(array $params = array(), $ignore = false)
	{
		$ret = $this->callCreateMethod($this->getService(), 'create', $params, $ignore);
		return $ret;
	}

	/**
	 * 通过主键，编辑一条记录
	 * @param integer $id
	 * @param array $params
	 * @return array
	 */
	public function modifyByPk($id, array $params = array())
	{
		$ret = $this->callModifyMethod($this->getService(), 'modifyByPk', $id, $params);
		return $ret;
	}

	/**
	 * 通过主键，删除一条记录
	 * @param integer $id
	 * @return array
	 */
	public function removeByPk($id)
	{
		$ret = $this->callRemoveMethod($this->getService(), 'removeByPk', $id);
		return $ret;
	}

	/**
	 * 通过主键，编辑多条记录。不支持联合主键
	 * @param array $values
	 * @param array $params
	 * @return integer
	 */
	public function batchModifyByPk(array $values, array $params = array())
	{
		$ret = $this->callModifyMethod($this->getService(), 'batchModifyByPk', $values, $params);
		return $ret;
	}

	/**
	 * 批量编辑排序
	 * @param array $params
	 * @return integer
	 */
	public function batchModifySort(array $params = array())
	{
		$rowCount = $this->getService()->batchModifySort($params);

		$errNo = ErrorNo::SUCCESS_NUM;
		$errMsg = ($rowCount > 0) ? Lang::_('ERROR_MSG_SUCCESS_UPDATE') : Lang::_('ERROR_MSG_ERROR_DB_AFFECTS_ZERO');
		Log::debug(sprintf(
			'%s callModifyMethod, service "%s", method "%s", rowCount "%d", params "%s"',
			$errMsg, get_class($this), 'batchModifySort', $rowCount, serialize($params)
		), $errNo, __METHOD__);

		return array(
			'err_no' => $errNo,
			'err_msg' => $errMsg,
			'row_count' => $rowCount
		);
	}

	/**
	 * 通过主键，删除多条记录。不支持联合主键
	 * @param array $ids
	 * @return array
	 */
	public function batchRemoveByPk(array $ids)
	{
		$ret = $this->callRemoveMethod($this->getService(), 'batchRemoveByPk', $ids);
		return $ret;
	}

	/**
	 * 通过主键，将一条记录移至回收站。不支持联合主键
	 * @param integer $id
	 * @return array
	 */
	public function trashByPk($id)
	{
		$ret = $this->callRemoveMethod($this->getService(), 'trashByPk', $id);
		return $ret;
	}

	/**
	 * 通过主键，将多条记录移至回收站。不支持联合主键
	 * @param array $ids
	 * @return array
	 */
	public function batchTrashByPk(array $ids)
	{
		$ret = $this->callRemoveMethod($this->getService(), 'batchTrashByPk', $ids);
		return $ret;
	}

	/**
	 * 通过主键，从回收站还原一条记录
	 * @param integer $pk
	 * @return integer
	 */
	public function restoreByPk($pk)
	{
		$ret = $this->callRestoreMethod($this->getService(), 'restoreByPk', $pk);
		return $ret;
	}

	/**
	 * 通过主键，将多条记录移至回收站。不支持联合主键
	 * @param array $ids
	 * @return integer
	 */
	public function batchRestoreByPk(array $ids)
	{
		$ret = $this->callRestoreMethod($this->getService(), 'batchRestoreByPk', $ids);
		return $ret;
	}

	/**
	 * 获取业务处理类
	 * @return instance of srv\srvname\services\Types
	 */
	public function getService()
	{
		if ($this->_service === null) {
			$this->_service = Service::getInstance($this->_className, $this->_srvName);
		}

		return $this->_service;
	}

	/**
	 * 过滤数组（只保留指定的字段）、清理数据并且清除空数据（空字符，负数）
	 * @param array $attributes
	 * @param array $rules
	 * @return void
	 */
	protected function filterCleanEmpty(array &$attributes = array(), array $rules = array())
	{
		$this->filterAttributes($attributes, array_keys($rules));
		$attributes = Clean::rules($rules, $attributes);
		foreach ($rules as $columnName => $funcName) {
			if (!isset($attributes[$columnName])) {
				continue;
			}

			if ($funcName === 'trim' && $attributes[$columnName] === '') {
				unset($attributes[$columnName]);
				continue;
			}

			if ($funcName === 'intval' && $attributes[$columnName] < 0) {
				unset($attributes[$columnName]);
				continue;
			}
		}
	}

	/**
	 * 获取缺省的最后一次访问的列表页链接
	 * @return string
	 */
	public function getLLUDefault()
	{
		return $this->urlManager->getUrl($this->actNameList, $this->controller, $this->module);
	}

	/**
	 * 获取最后一次访问的列表页链接
	 * @return string
	 */
	public function getLLU()
	{
		$urls = $this->getLLUs();
		$router = $this->module . '_' . $this->controller . '_' . $this->actNameList;
		if (isset($urls[$router])) {
			return $urls[$router];
		}

		return $this->getLLUDefault();
	}

	/**
	 * 设置最后一次访问的列表页链接
	 * @param array $params
	 * @return void
	 */
	public function setLLU(array $params = array())
	{
		$urls = $this->getLLUs();
		$router = $this->module . '_' . $this->controller . '_' . $this->actNameList;
		$urls[$router] = $this->urlManager->getUrl($this->action, $this->controller, $this->module, $params);
		while (count($urls) > self::LLU_COOKIE_COUNT) {
			array_shift($urls);
		}

		$value = str_replace('=', '', base64_encode(serialize($urls)));
		HttpCookie::add(self::LLU_COOKIE_NAME, $value);
	}

	/**
	 * 获取Cookie中所有的列表页链接
	 * @return array
	 */
	public function getLLUs()
	{
		$value = HttpCookie::get(self::LLU_COOKIE_NAME);
		if ($value !== null) {
			$urls = unserialize(base64_decode($value));
			if (is_array($urls)) {
				return $urls;
			}
		}

		return array();
	}

	/**
	 * 获取Input表单元素分类标签，需要子类重写此方法
	 * @return array
	 */
	public function getViewTabsRender()
	{
		return array();
	}

	/**
	 * 获取表单元素配置，需要子类重写此方法
	 * @return array
	 */
	public function getElementsRender()
	{
		return array();
	}
}
