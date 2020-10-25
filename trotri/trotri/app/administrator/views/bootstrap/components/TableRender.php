<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace views\bootstrap\components;

use library\BaseModel;

/**
 * TableRender class file
 * 表格渲染基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: TableRender.php 1 2013-05-18 14:58:59Z huan.song $
 * @package views.bootstrap.components
 * @since 1.0
 */
class TableRender
{
	/**
	 * @var \library\BaseModel 表单元素管理类
	 */
	public $elements_object = null;

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
		$actNameList,
		$actNameView,
		$actNameCreate,
		$actNameModify,
		$actNameRemove,
		$actNameTrash = 'trash';

	/**
	 * 构造方法，初始化模板解析类、URL管理类、页面辅助类、模型名、控制器名、方法名、缺省的列表页方法名、缺省的详情页方法名、缺省的新增数据方法名、缺省的编辑数据方法名
	 * @param \library\BaseModel $elements
	 */
	public function __construct(BaseModel $elements)
	{
		$this->elements_object = $elements;

		$this->view = $this->elements_object->view;
		$this->urlManager = $this->elements_object->urlManager;
		$this->html = $this->elements_object->html;
		$this->module = $this->elements_object->module;
		$this->controller = $this->elements_object->controller;
		$this->action = $this->elements_object->action;
		$this->actNameList = $this->elements_object->actNameList;
		$this->actNameView = $this->elements_object->actNameView;
		$this->actNameCreate = $this->elements_object->actNameCreate;
		$this->actNameModify = $this->elements_object->actNameModify;
		$this->actNameRemove = $this->elements_object->actNameRemove;
	}

	/**
	 * 获取“编辑”图标
	 * @param array $params
	 * @return array
	 */
	public function getModifyIcon(array $params)
	{
		return ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_MODIFY,
			'url' => $this->urlManager->getUrl($this->actNameModify, $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_MODIFY,
		));
	}

	/**
	 * 获取“彻底删除”图标
	 * @param array $params
	 * @return array
	 */
	public function getRemoveIcon(array $params)
	{
		return ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_REMOVE,
			'url' => $this->urlManager->getUrl($this->actNameRemove, $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGREMOVE,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_REMOVE,
		));
	}

	/**
	 * 获取“移至回收站”图标
	 * @param array $params
	 * @return array
	 */
	public function getTrashIcon(array $params)
	{
		return ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_TRASH,
			'url' => $this->urlManager->getUrl($this->actNameTrash, $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_DIALOGTRASH,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_TRASH,
		));
	}

	/**
	 * 获取“恢复”图标
	 * @param array $params
	 * @return array
	 */
	public function getRestoreIcon(array $params)
	{
		$params['is_restore'] = '1';
		return ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_RESTORE,
			'url' => $this->urlManager->getUrl($this->actNameTrash, $this->controller, $this->module, $params),
			'jsfunc' => ComponentsConstant::JSFUNC_HREF,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_RESTORE,
		));
	}

	/**
	 * 获取“预览”图标
	 * @param array $params
	 * @return array
	 */
	public function getPreviewIcon(array $params)
	{
		return ComponentsBuilder::getGlyphicon(array(
			'type' => ComponentsConstant::GLYPHICON_LINK,
			'url' => $params['url'],
			'jsfunc' => ComponentsConstant::JSFUNC_BHREF,
			'title' => $this->view->CFG_SYSTEM_GLOBAL_PREVIEW,
		));
	}
}
