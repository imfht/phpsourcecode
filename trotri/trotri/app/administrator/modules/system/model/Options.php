<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\system\model;

use library\BaseModel;
use tfc\util\String;
use tfc\saf\Text;
use system\services\DataOptions;

/**
 * Options class file
 * 站点配置
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Options.php 1 2014-08-19 23:04:36Z Code Generator $
 * @package modules.system.model
 * @since 1.0
 */
class Options extends BaseModel
{
	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::getViewTabsRender()
	 */
	public function getViewTabsRender()
	{
		$output = array(
			'register' => array(
				'tid' => 'register',
				'prompt' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_VIEWTAB_REGISTER_PROMPT')
			),
			'picture' => array(
				'tid' => 'picture',
				'prompt' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_VIEWTAB_PICTURE_PROMPT')
			),
			'smtp' => array(
				'tid' => 'smtp',
				'prompt' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_VIEWTAB_SMTP_PROMPT')
			),
			'paginator' => array(
				'tid' => 'paginator',
				'prompt' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_VIEWTAB_PAGINATOR_PROMPT')
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
			'site_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SITE_NAME_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SITE_NAME_HINT'),
				'required' => true,
			),
			'site_url' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SITE_URL_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SITE_URL_HINT'),
				'required' => true,
			),
			'tpl_dir' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_TPL_DIR_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_TPL_DIR_HINT'),
				'required' => true,
			),
			'html_dir' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_HTML_DIR_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_HTML_DIR_HINT'),
				'required' => true,
			),
			'meta_title' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_META_TITLE_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_META_TITLE_HINT'),
			),
			'meta_keywords' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_META_KEYWORDS_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_META_KEYWORDS_HINT'),
			),
			'meta_description' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_META_DESCRIPTION_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_META_DESCRIPTION_HINT'),
			),
			'powerby' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_POWERBY_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_POWERBY_HINT'),
			),
			'stat_code' => array(
				'__tid__' => 'main',
				'type' => 'textarea',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_STAT_CODE_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_STAT_CODE_HINT'),
			),
			'url_rewrite' => array(
				'__tid__' => 'main',
				'type' => 'switch',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_URL_REWRITE_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_URL_REWRITE_HINT'),
				'options' => DataOptions::getUrlRewriteEnum(),
				'value' => DataOptions::URL_REWRITE_N,
			),
			'close_register' => array(
				'__tid__' => 'register',
				'type' => 'switch',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_CLOSE_REGISTER_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_CLOSE_REGISTER_HINT'),
				'options' => DataOptions::getCloseRegisterEnum(),
				'value' => DataOptions::CLOSE_REGISTER_N,
			),
			'close_register_reason' => array(
				'__tid__' => 'register',
				'type' => 'textarea',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_CLOSE_REGISTER_REASON_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_CLOSE_REGISTER_REASON_HINT'),
				'rows' => 10
			),
			'show_register_service_item' => array(
				'__tid__' => 'register',
				'type' => 'switch',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SHOW_REGISTER_SERVICE_ITEM_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SHOW_REGISTER_SERVICE_ITEM_HINT'),
				'options' => DataOptions::getShowRegisterServiceItemEnum(),
				'value' => DataOptions::SHOW_REGISTER_SERVICE_ITEM_Y,
			),
			'register_service_item' => array(
				'__tid__' => 'register',
				'type' => 'textarea',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_REGISTER_SERVICE_ITEM_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_REGISTER_SERVICE_ITEM_HINT'),
				'rows' => 20
			),
			'thumb_width' => array(
				'__tid__' => 'picture',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_THUMB_WIDTH_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_THUMB_WIDTH_HINT'),
			),
			'thumb_height' => array(
				'__tid__' => 'picture',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_THUMB_HEIGHT_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_THUMB_HEIGHT_HINT'),
			),
			'water_mark_type' => array(
				'__tid__' => 'picture',
				'type' => 'radio',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_TYPE_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_TYPE_HINT'),
				'options' => DataOptions::getWaterMarkTypeEnum(),
				'value' => DataOptions::WATER_MARK_TYPE_NONE,
			),
			'water_mark_imgdir' => array(
				'__tid__' => 'picture',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_IMGDIR_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_IMGDIR_HINT'),
			),
			'water_mark_text' => array(
				'__tid__' => 'picture',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_TEXT_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_TEXT_HINT'),
			),
			'water_mark_position' => array(
				'__tid__' => 'picture',
				'type' => 'radio',
				'__object__' => 'views\\bootstrap\\system\\WaterMarkPositionRadioElement',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_POSITION_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_POSITION_HINT'),
				'options' => DataOptions::getWaterMarkPositionEnum(),
				'value' => DataOptions::WATER_MARK_POSITION_9,
			),
			'water_mark_pct' => array(
				'__tid__' => 'picture',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_PCT_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_WATER_MARK_PCT_HINT'),
				'value' => 0,
			),
			'smtp_host' => array(
				'__tid__' => 'smtp',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_HOST_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_HOST_HINT'),
			),
			'smtp_port' => array(
				'__tid__' => 'smtp',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_PORT_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_PORT_HINT'),
				'value' => 25
			),
			'smtp_username' => array(
				'__tid__' => 'smtp',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_USERNAME_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_USERNAME_HINT'),
			),
			'smtp_password' => array(
				'__tid__' => 'smtp',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_PASSWORD_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_PASSWORD_HINT'),
			),
			'smtp_frommail' => array(
				'__tid__' => 'smtp',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_FROMMAIL_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_SMTP_FROMMAIL_HINT'),
			),
			'list_rows_posts' => array(
				'__tid__' => 'paginator',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_LIST_ROWS_POSTS_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_LIST_ROWS_POSTS_HINT'),
			),
			'list_rows_post_comments' => array(
				'__tid__' => 'paginator',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_LIST_ROWS_POST_COMMENTS_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_LIST_ROWS_POST_COMMENTS_HINT'),
			),
			'list_rows_users' => array(
				'__tid__' => 'paginator',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_LIST_ROWS_USERS_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_OPTIONS_LIST_ROWS_USERS_HINT'),
			),
		);

		return $output;
	}

	/**
	 * 获取所有的配置，以键值对方式返回
	 * @return array
	 */
	public function findPairs()
	{
		$ret = $this->callFetchMethod($this->getService(), 'findPairs');
		return $ret;
	}

	/**
	 * 通过键名，编辑多条记录，如果键名不存在则新增记录
	 * @param array $params
	 * @return integer
	 */
	public function batchReplace(array $params = array())
	{
		if (isset($params['stat_code'])) {
			$params['stat_code'] = String::stripslashes($params['stat_code']);
		}

		if (isset($params['powerby'])) {
			$params['powerby'] = String::stripslashes($params['powerby']);
		}

		$ret = $this->callModifyMethod($this->getService(), 'batchReplaceById', 0, $params);
		return $ret;
	}

}
