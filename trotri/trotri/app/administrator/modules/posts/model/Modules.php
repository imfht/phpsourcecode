<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\posts\model;

use library\BaseModel;
use tfc\saf\Text;
use posts\services\DataModules;

/**
 * Modules class file
 * 模型管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Modules.php 1 2014-10-12 22:12:36Z Code Generator $
 * @package modules.posts.model
 * @since 1.0
 */
class Modules extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
		);

		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getElementsRender()
	 */
	public function getElementsRender()
	{
		$output = array(
			'module_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_POSTS_POST_MODULES_MODULE_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_MODULES_MODULE_ID_HINT'),
			),
			'module_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_MODULES_MODULE_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_MODULES_MODULE_NAME_HINT'),
				'required' => true,
			),
			'fields' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POSTS_POST_MODULES_FIELDS_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_MODULES_FIELDS_HINT'),
				'rows' => 15
			),
			'forbidden' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_POSTS_POST_MODULES_FORBIDDEN_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_MODULES_FORBIDDEN_HINT'),
				'options' => DataModules::getForbiddenEnum(),
				'value' => DataModules::FORBIDDEN_N,
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POSTS_POST_MODULES_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_MODULES_DESCRIPTION_HINT'),
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“模型名称”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getModuleNameLink($data)
	{
		$params = array(
			'id' => $data['module_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['module_name'], $url);
		return $output;
	}

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::findByPk()
	 */
	public function findByPk($value)
	{
		$ret = parent::findByPk($value);
		if ($ret && is_array($ret)) {
			if (isset($ret['data']) && is_array($ret['data'])) {
				if (isset($ret['data']['fields']) && is_array($ret['data']['fields'])) {
					$lines = '';
					foreach ($ret['data']['fields'] as $name => $row) {
						$lines .= $name . '|' . $row['label'] . (($row['hint'] !== '') ? ('|' . $row['hint']) : '') . "\n";
					}

					$ret['data']['fields'] = rtrim($lines);
				}
			}
		}

		return $ret;
	}

	/**
	 * 获取所有的模型名称
	 * @return array
	 */
	public function getModuleNames()
	{
		return $this->getService()->getModuleNames();
	}

	/**
	 * 获取所有的文档扩展字段
	 * @return array
	 */
	public function getFields()
	{
		return $this->getService()->getFields();
	}

	/**
	 * 通过“主键ID”，获取“模型名称”
	 * @param integer $moduleId
	 * @return string
	 */
	public function getModuleNameByModuleId($moduleId)
	{
		return $this->getService()->getModuleNameByModuleId($moduleId);
	}
}
