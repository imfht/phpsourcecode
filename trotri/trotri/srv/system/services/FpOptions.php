<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2014 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace system\services;

use libsrv\FormProcessor;
use tfc\validator;
use system\library\Lang;

/**
 * FpOptions class file
 * 业务层：表单数据处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: FpOptions.php 1 2014-08-19 00:15:56Z Code Generator $
 * @package system.services
 * @since 1.0
 */
class FpOptions extends FormProcessor
{
	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::run()
	 */
	public function run(array $params)
	{
		$this->clearValues();
		$this->clearErrors();

		$params = $this->_cleanPreProcess($params);
		if ($params === false) {
			return false;
		}

		if ($this->_process($params)) {
			return $this->_cleanPostProcess();
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_process()
	 */
	protected function _process(array $params = array())
	{
		$this->isValids($params,
			'site_name', 'site_url', /* 'tpl_dir', 'html_dir', */ 'meta_title', 'meta_keywords', 'meta_description', 'powerby', 'stat_code', 'url_rewrite',
			'close_register', 'close_register_reason', 'show_register_service_item', 'register_service_item',
			'thumb_width', 'thumb_height', 'water_mark_type', 'water_mark_imgdir', 'water_mark_text', 'water_mark_position', 'water_mark_pct',
			'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_frommail',
			'list_rows_posts', 'list_rows_post_comments', 'list_rows_users');
		return !$this->hasError();
	}

	/**
	 * (non-PHPdoc)
	 * @see \libsrv\FormProcessor::_cleanPreProcess()
	 */
	protected function _cleanPreProcess(array $params)
	{
		$rules = array(
			'site_name' => 'trim',
			'site_url' => 'trim',
			'tpl_dir' => 'trim',
			'html_dir' => 'trim',
			'meta_title' => 'trim',
			'meta_keywords' => 'trim',
			'powerby' => 'trim',
			'url_rewrite' => 'trim',
			'close_register' => 'trim',
			'show_register_service_item' => 'trim',
			'thumb_width' => 'intval',
			'thumb_height' => 'intval',
			'water_mark_type' => 'trim',
			'water_mark_imgdir' => 'trim',
			'water_mark_text' => 'trim',
			'water_mark_position' => 'intval',
			'water_mark_pct' => 'intval',
			'smtp_host' => 'trim',
			'smtp_port' => 'intval',
			'smtp_username' => 'trim',
			'smtp_password' => 'trim',
			'smtp_frommail' => 'trim',
			'list_rows_posts' => 'intval',
			'list_rows_post_comments' => 'intval',
			'list_rows_users' => 'intval',
		);

		$ret = $this->clean($rules, $params);
		return $ret;
	}

	/**
	 * 获取“网站名称”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getSiteNameRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_SITE_NAME_NOTEMPTY')),
			'MinLength' => new validator\MinLengthValidator($value, 2, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_SITE_NAME_MINLENGTH')),
			'MaxLength' => new validator\MaxLengthValidator($value, 100, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_SITE_NAME_MAXLENGTH')),
		);
	}

	/**
	 * 获取“网站URL”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getSiteUrlRule($value)
	{
		return array(
			'NotEmpty' => new validator\NotEmptyValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_SITE_URL_NOTEMPTY')),
			'Url' => new validator\UrlValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_SITE_URL_URL')),
		);
	}

	/**
	 * 获取“模板名称”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getTplDirRule($value)
	{
		return array(
			'AlphaNum' => new validator\AlphaNumValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_TPL_DIR_ALPHANUM')),
		);
	}

	/**
	 * 获取“生成静态页面存放目录名称”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getHtmlDirRule($value)
	{
		return array(
			'AlphaNum' => new validator\AlphaNumValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_HTML_DIR_ALPHANUM')),
		);
	}

	/**
	 * 获取“使用重写模式获取URLS”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getUrlRewriteRule($value)
	{
		$enum = DataOptions::getUrlRewriteEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_SYSTEM_OPTIONS_URL_REWRITE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否关闭新用户注册”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getCloseRegisterRule($value)
	{
		$enum = DataOptions::getCloseRegisterEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_SYSTEM_OPTIONS_CLOSE_REGISTER_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“是否显示用户注册协议”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getShowRegisterServiceItemRule($value)
	{
		$enum = DataOptions::getShowRegisterServiceItemEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_SYSTEM_OPTIONS_SHOW_REGISTER_SERVICE_ITEM_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“缩略图宽”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getThumbWidthRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_THUMB_WIDTH_INTEGER')),
		);
	}

	/**
	 * 获取“缩略图高”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getThumbHeightRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_THUMB_HEIGHT_INTEGER')),
		);
	}

	/**
	 * 获取“水印类型”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getWaterMarkTypeRule($value)
	{
		$enum = DataOptions::getWaterMarkTypeEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_SYSTEM_OPTIONS_WATER_MARK_TYPE_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“水印放置位置”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getWaterMarkPositionRule($value)
	{
		$enum = DataOptions::getWaterMarkPositionEnum();
		return array(
			'InArray' => new validator\InArrayValidator($value, array_keys($enum), sprintf(Lang::_('SRV_FILTER_SYSTEM_OPTIONS_WATER_MARK_POSITION_INARRAY'), implode(', ', $enum))),
		);
	}

	/**
	 * 获取“水印融合度”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getWaterMarkPctRule($value)
	{
		return array(
			'Numeric' => new validator\NumericValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_WATER_MARK_PCT_NUMERIC')),
			'Min' => new validator\MinValidator($value, 0, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_WATER_MARK_PCT_MIN')),
			'Max' => new validator\MaxValidator($value, 100, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_WATER_MARK_PCT_MAX')),
		);
	}

	/**
	 * 获取“SMTP服务器端口”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getSmtpPortRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_SMTP_PORT_INTEGER')),
		);
	}

	/**
	 * 获取“文档列表每页展示条数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getListRowsPostsRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_LIST_ROWS_POSTS_INTEGER')),
		);
	}

	/**
	 * 获取“文档评论列表每页展示条数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getListRowsPostCommentsRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_LIST_ROWS_POST_COMMENTS_INTEGER')),
		);
	}

	/**
	 * 获取“用户列表每页展示条数”验证规则
	 * @param mixed $value
	 * @return array
	 */
	public function getListRowsUsersRule($value)
	{
		return array(
			'Integer' => new validator\IntegerValidator($value, true, Lang::_('SRV_FILTER_SYSTEM_OPTIONS_LIST_ROWS_USERS_INTEGER')),
		);
	}

}
