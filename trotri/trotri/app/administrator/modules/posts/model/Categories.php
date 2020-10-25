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
use libapp\Model;

/**
 * Categories class file
 * 类别管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Categories.php 1 2014-10-13 22:24:54Z Code Generator $
 * @package modules.posts.model
 * @since 1.0
 */
class Categories extends BaseModel
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
			'category_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_CATEGORY_ID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_CATEGORY_ID_HINT'),
			),
			'category_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_CATEGORY_NAME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_CATEGORY_NAME_HINT'),
				'required' => true,
			),
			'category_pid' => array(
				'__tid__' => 'main',
				'type' => 'select',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_CATEGORY_PID_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_CATEGORY_PID_HINT'),
				'required' => true,
			),
			'alias' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_ALIAS_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_ALIAS_HINT'),
			),
			'meta_title' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_META_TITLE_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_META_TITLE_HINT'),
				'required' => true,
			),
			'meta_keywords' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_META_KEYWORDS_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_META_KEYWORDS_HINT'),
				'required' => true,
			),
			'meta_description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_META_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_META_DESCRIPTION_HINT'),
				'required' => true,
			),
			'tpl_home' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_TPL_HOME_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_TPL_HOME_HINT'),
				'required' => true,
				'value' => 'home'
			),
			'tpl_list' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_TPL_LIST_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_TPL_LIST_HINT'),
				'required' => true,
				'value' => 'index'
			),
			'tpl_view' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_TPL_VIEW_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_TPL_VIEW_HINT'),
				'required' => true,
				'value' => 'view'
			),
			'sort' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_SORT_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_SORT_HINT'),
				'required' => true,
				'value' => 15
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_POSTS_POST_CATEGORIES_DESCRIPTION_HINT'),
			),
			'post_count' => array(
				'label' => Text::_('MOD_POSTS_POST_CATEGORIES_POST_COUNT_LABEL'),
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“类别名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getCategoryNameLink($data)
	{
		$params = array(
			'id' => $data['category_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['category_name'], $url);
		return $output;
	}

	/**
	 * 递归方式获取所有的类别，默认用|—填充子类别左边用于和父类别错位（可用于Table列表）
	 * @param integer $categoryPid
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function findLists($categoryPid = 0, $padStr = '|—', $leftPad = '', $rightPad = null)
	{
		$ret = $this->callFetchMethod($this->getService(), 'findLists', array($categoryPid, $padStr, $leftPad, $rightPad));
		return $ret;
	}

	/**
	 * 递归方式获取所有的类别名，默认用空格填充子类别左边用于和父类别错位
	 * （只返回ID和类别名的键值对）（可用于Select表单的Option选项）
	 * @param integer $categoryPid
	 * @param string $padStr
	 * @param string $leftPad
	 * @param string $rightPad
	 * @return array
	 */
	public function getOptions($categoryPid = -1, $padStr = '&nbsp;&nbsp;&nbsp;&nbsp;', $leftPad = '', $rightPad = null)
	{
		return $this->getService()->getOptions($categoryPid, $padStr, $leftPad, $rightPad);
	}

	/**
	 * 获取所有的ModuleName
	 * @return array
	 */
	public function getModuleNames()
	{
		return Model::getInstance('Modules')->getModuleNames();
	}

	/**
	 * 通过“主键ID”，获取“模型名称”
	 * @param integer $moduleId
	 * @return string
	 */
	public function getModuleNameByModuleId($moduleId)
	{
		return Model::getInstance('Modules')->getModuleNameByModuleId($moduleId);
	}

	/**
	 * 获取指定类别下的文档数
	 * @param integer $categoryId
	 * @return integer
	 */
	public function getPostCount($categoryId)
	{
		return $this->getService()->getPostCount($categoryId);
	}

}
