<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\builder\model;

use library\BaseModel;
use tfc\saf\Text;
use builders\services\DataBuilders;
use library\Constant;
use modules\builder\model\gc\Generator;

/**
 * Builders class file
 * 生成代码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Builders.php 1 2014-05-26 19:25:19Z Code Generator $
 * @package modules.builder.model
 * @since 1.0
 */
class Builders extends BaseModel
{
	/**
	 * @var string 业务层名
	 */
	protected $_srvName = Constant::SRV_NAME_BUILDERS;

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'act' => array(
				'tid' => 'act',
				'prompt' => Text::_('MOD_BUILDER_BUILDERS_VIEWTAB_ACT_PROMPT')
			),
			'system' => array(
				'tid' => 'system',
				'prompt' => Text::_('MOD_BUILDER_BUILDERS_VIEWTAB_SYSTEM_PROMPT')
			),
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
			'builder_id' => array(
				'__tid__' => 'main',
				'type' => 'hidden',
				'label' => Text::_('MOD_BUILDER_BUILDERS_BUILDER_ID_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_BUILDER_ID_HINT'),
			),
			'builder_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_BUILDER_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_BUILDER_NAME_HINT'),
				'required' => true,
			),
			'tbl_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_TBL_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_TBL_NAME_HINT'),
				'required' => true,
			),
			'tbl_profile' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDERS_TBL_PROFILE_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_TBL_PROFILE_HINT'),
				'options' => DataBuilders::getTblProfileEnum(),
				'value' => DataBuilders::TBL_PROFILE_N,
			),
			'tbl_engine' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_BUILDER_BUILDERS_TBL_ENGINE_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_TBL_ENGINE_HINT'),
				'options' => DataBuilders::getTblEngineEnum(),
				'value' => DataBuilders::TBL_ENGINE_INNODB,
			),
			'tbl_charset' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_BUILDER_BUILDERS_TBL_CHARSET_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_TBL_CHARSET_HINT'),
				'options' => DataBuilders::getTblCharsetEnum(),
				'value' => DataBuilders::TBL_CHARSET_UTF8,
			),
			'tbl_comment' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_TBL_COMMENT_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_TBL_COMMENT_HINT'),
				'required' => true,
			),
			'srv_type' => array(
				'__tid__' => 'main',
				'type' => 'radio',
				'label' => Text::_('MOD_BUILDER_BUILDERS_SRV_TYPE_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_SRV_TYPE_HINT'),
				'options' => DataBuilders::getSrvTypeEnum(),
				'value' => DataBuilders::SRV_TYPE_NORMAL,
			),
			'srv_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_SRV_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_SRV_NAME_HINT'),
				'required' => true,
			),
			'app_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_APP_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_APP_NAME_HINT'),
				'required' => true,
			),
			'mod_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_MOD_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_MOD_NAME_HINT'),
				'required' => true,
			),
			'cls_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_CLS_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_CLS_NAME_HINT'),
				'required' => true,
			),
			'ctrl_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_CTRL_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_CTRL_NAME_HINT'),
				'required' => true,
			),
			'fk_column' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_FK_COLUMN_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_FK_COLUMN_HINT'),
			),
			'act_index_name' => array(
				'__tid__' => 'act',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_ACT_INDEX_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_ACT_INDEX_NAME_HINT'),
				'value' => 'index',
				'required' => true,
			),
			'act_view_name' => array(
				'__tid__' => 'act',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_ACT_VIEW_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_ACT_VIEW_NAME_HINT'),
				'value' => 'view',
				'required' => true,
			),
			'act_create_name' => array(
				'__tid__' => 'act',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_ACT_CREATE_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_ACT_CREATE_NAME_HINT'),
				'value' => 'create',
				'required' => true,
			),
			'act_modify_name' => array(
				'__tid__' => 'act',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_ACT_MODIFY_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_ACT_MODIFY_NAME_HINT'),
				'value' => 'modify',
				'required' => true,
			),
			'act_remove_name' => array(
				'__tid__' => 'act',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_ACT_REMOVE_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_ACT_REMOVE_NAME_HINT'),
				'value' => 'remove',
				'required' => true,
			),
			'index_row_btns' => array(
				'__tid__' => 'main',
				'type' => 'checkbox',
				'label' => Text::_('MOD_BUILDER_BUILDERS_INDEX_ROW_BTNS_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_INDEX_ROW_BTNS_HINT'),
				'options' => DataBuilders::getIndexRowBtnsEnum(),
			),
			'description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_BUILDER_BUILDERS_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_DESCRIPTION_HINT'),
			),
			'author_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_AUTHOR_NAME_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_AUTHOR_NAME_HINT'),
				'required' => true,
			),
			'author_mail' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_AUTHOR_MAIL_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_AUTHOR_MAIL_HINT'),
				'required' => true,
			),
			'dt_created' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_DT_CREATED_HINT'),
				'disabled' => true,
			),
			'dt_modified' => array(
				'__tid__' => 'system',
				'type' => 'text',
				'label' => Text::_('MOD_BUILDER_BUILDERS_DT_MODIFIED_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_DT_MODIFIED_HINT'),
				'disabled' => true,
			),
			'trash' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_BUILDER_BUILDERS_TRASH_LABEL'),
				'hint' => Text::_('MOD_BUILDER_BUILDERS_TRASH_HINT'),
				'options' => DataBuilders::getTrashEnum(),
				'value' => DataBuilders::TRASH_Y,
			),
		);

		return $output;
	}

	/**
	 * 获取列表页“生成代码名”的A标签
	 * @param array $data
	 * @return string
	 */
	public function getBuilderNameLink($data)
	{
		$params = array(
			'id' => $data['builder_id'],
		);

		$url = $this->urlManager->getUrl($this->actNameView, $this->controller, $this->module, $params);
		$output = $this->html->a($data['builder_name'], $url);
		return $output;
	}

	/**
	 * 通过“生成代码ID”获取“生成代码名”
	 * @param integer $builderId
	 * @return string
	 */
	public function getBuilderNameByBuilderId($builderId)
	{
		$ret = $this->getService()->getBuilderNameByBuilderId($builderId);
		return $ret;
	}

	/**
	 * 获取是否生成扩展表
	 * @param string $tblProfile
	 * @return string
	 */
	public function getTblProfileLangByTblProfile($tblProfile)
	{
		$ret = $this->getService()->getTblProfileLangByTblProfile($tblProfile);
		return $ret;
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
		$rules = array(
			'builder_id' => 'intval',
			'builder_name' => 'trim',
			'tbl_name' => 'trim',
			'tbl_profile' => 'trim',
			'tbl_engine' => 'trim',
			'tbl_charset' => 'trim',
			'app_name' => 'trim',
			'trash' => 'trim',
			'author_name' => 'trim',
			'author_mail' => 'trim',
		);

		$this->filterCleanEmpty($params, $rules);
		$ret = parent::search($params, 'builder_id DESC', $limit, $offset);
		return $ret;
	}

	/**
	 * 获取所有的表名
	 * @return array
	 */
	public function getTblNames()
	{
		$ret = $this->getService()->getTblNames();
		return $ret;
	}

	/**
	 * 通过Builders数据生成代码
	 * @param integer $builderId
	 * @return void
	 */
	public function gc($builderId)
	{
		$generator = new Generator($builderId);
		$generator->run();
	}
}
