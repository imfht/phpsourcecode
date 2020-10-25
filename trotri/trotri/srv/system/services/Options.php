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

use libsrv\AbstractService;
use tfc\ap\Ap;
use tdo\Metadata;
use system\library\Lang;

/**
 * Options class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Options.php 1 2014-08-19 00:15:56Z Code Generator $
 * @package system.services
 * @since 1.0
 */
class Options extends AbstractService
{
	/**
	 * @var instance of system\services\Options
	 */
	protected static $_instance = null;

	/**
	 * 获取本类的实例化对象
	 * @return system\services\Options
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * 获取所有的配置，以键值对方式返回
	 * @return array
	 */
	public function findPairs()
	{
		$rows = $this->getDb()->findPairs();
		return $rows;
	}

	/**
	 * 通过键名，编辑多条记录，如果键名不存在则新增记录
	 * @param integer $id
	 * @param array $params
	 * @return integer
	 */
	public function batchReplaceById($id, array $params = array())
	{
		return $this->batchReplace($params);
	}

	/**
	 * 通过键名，编辑多条记录，如果键名不存在则新增记录
	 * @param array $params
	 * @return integer
	 */
	public function batchReplace(array $params = array())
	{
		$formProcessor = $this->getFormProcessor();
		if (!$formProcessor->run($params)) {
			return false;
		}

		$attributes = $formProcessor->getValues();
		$rowCount = $this->getDb()->batchReplace($attributes);
		return $rowCount;
	}

	/**
	 * 获取系统信息
	 * @return array
	 */
	public static function getSysInfo()
	{
		static $metadata = null;
		if ($metadata === null) {
			$metadata = new Metadata(self::getInstance()->getDb()->getDbProxy());
		}

		$data = array(
			'tfcversion' => Ap::getVersion(),
			'dbversion'  => $metadata->getVersion(),
			'phpversion' => PHP_OS . ' / PHP v' . PHP_VERSION . (@ini_get('safe_mode') ? ' Safe Mode' : ''),
			'software'   => Ap::getRequest()->getServer('SERVER_SOFTWARE'),
			'maxupsize'  => @ini_get('file_uploads') ? ini_get('upload_max_filesize') : Lang::_('SRV_ENUM_GLOBAL_UNKOWN')
		);

		return $data;
	}

	/**
	 * 获取开发者信息
	 * @return array
	 */
	public static function getDevInfo()
	{
		$data = array(
			'author' => 'Huan Song <a href="mailto:trotri@yeah.net">trotri@yeah.net</a>',
			'copyright' => 'Copyright &copy; 2011-2013 <a href="http://www.trotri.com/" target="_blank">http://www.trotri.com/</a> All rights reserved.',
			'license' => 'http://www.apache.org/licenses/LICENSE-2.0',
			'team' => '',
			'skins' => '<a href="http://www.bootcss.com/" target="_blank">Bootstrap</a>',
			'thanks' => '',
			'links' => array(
				'<a href="http://www.trotri.com/" target="_blank">' . Lang::_('SRV_ENUM_GLOBAL_LINKS_WEBSITE') . '</a>',
				'<a href="http://github.com/trotri/trotri" target="_blank">' . Lang::_('SRV_ENUM_GLOBAL_LINKS_GIT') . '</a>',
			)
		);

		return $data;
	}

	/**
	 * 通过键名，获取配置值
	 * @param string $optKey
	 * @return mixed
	 */
	public static function getValueByKey($optKey)
	{
		static $data = null;

		if ($data === null) {
			$data = self::getInstance()->findPairs();
		}

		$optValue = isset($data[$optKey]) ? $data[$optKey] : false;
		return $optValue;
	}

	/**
	 * 获取“网站名称”
	 * @return string
	 */
	public static function getSiteName()
	{
		$value = self::getValueByKey('site_name');
		return $value ? $value : '';
	}

	/**
	 * 获取“网站URL”
	 * @return string
	 */
	public static function getSiteUrl()
	{
		$value = self::getValueByKey('site_url');
		return $value ? $value : '';
	}

	/**
	 * 获取“模板名称”
	 * @return string
	 */
	public static function getTplDir()
	{
		$value = self::getValueByKey('tpl_dir');
		return $value ? $value : '';
	}

	/**
	 * 获取“生成静态页面存放目录名称”
	 * @return string
	 */
	public static function getHtmlDir()
	{
		$value = self::getValueByKey('html_dir');
		return $value ? $value : '';
	}

	/**
	 * 获取“SEO Title”
	 * @return string
	 */
	public static function getMetaTitle()
	{
		$value = self::getValueByKey('meta_title');
		return $value ? $value : '';
	}

	/**
	 * 获取“SEO Keywords”
	 * @return string
	 */
	public static function getMetaKeywords()
	{
		$value = self::getValueByKey('meta_keywords');
		return $value ? $value : '';
	}

	/**
	 * 获取“SEO Description”
	 * @return string
	 */
	public static function getMetaDescription()
	{
		$value = self::getValueByKey('meta_description');
		return $value ? $value : '';
	}

	/**
	 * 获取“网站版权信息”
	 * @return string
	 */
	public static function getPowerby()
	{
		$value = self::getValueByKey('powerby');
		return $value ? $value : '';
	}

	/**
	 * 获取“网站第三方统计代码”
	 * @return string
	 */
	public static function getStatCode()
	{
		$value = self::getValueByKey('stat_code');
		return $value ? $value : '';
	}

	/**
	 * 获取“是否使用重写模式获取URLS”
	 * @return boolean
	 */
	public static function isUrlRewrite()
	{
		$value = self::getValueByKey('url_rewrite');
		return $value ? ($value === DataOptions::URL_REWRITE_Y ? true : false) : null;
	}

	/**
	 * 获取“是否关闭新用户注册”
	 * @return boolean
	 */
	public static function isCloseRegister()
	{
		$value = self::getValueByKey('close_register');
		return $value ? ($value === DataOptions::CLOSE_REGISTER_Y ? true : false) : null;
	}

	/**
	 * 获取“关闭注册原因”
	 * @return string
	 */
	public static function getCloseRegisterReason()
	{
		$value = self::getValueByKey('close_register_reason');
		return $value ? $value : '';
	}

	/**
	 * 获取“是否显示用户注册协议”
	 * @return boolean
	 */
	public static function isShowRegisterServiceItem()
	{
		$value = self::getValueByKey('show_register_service_item');
		return $value ? ($value === DataOptions::SHOW_REGISTER_SERVICE_ITEM_Y ? true : false) : null;
	}

	/**
	 * 获取“用户注册协议”
	 * @return string
	 */
	public static function getRegisterServiceItem()
	{
		$value = self::getValueByKey('register_service_item');
		return $value ? $value : '';
	}

	/**
	 * 获取“缩略图宽”
	 * @return integer
	 */
	public static function getThumbWidth()
	{
		$value = self::getValueByKey('thumb_width');
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“缩略图高”
	 * @return integer
	 */
	public static function getThumbHeight()
	{
		$value = self::getValueByKey('thumb_height');
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“水印类型”
	 * @return string
	 */
	public static function getWaterMarkType()
	{
		$value = self::getValueByKey('water_mark_type');
		return $value ? $value : '';
	}

	/**
	 * 获取“水印图片文件地址”
	 * @return string
	 */
	public static function getWaterMarkImgdir()
	{
		$value = self::getValueByKey('water_mark_imgdir');
		return $value ? $value : '';
	}

	/**
	 * 获取“水印文字信息”
	 * @return string
	 */
	public static function getWaterMarkText()
	{
		$value = self::getValueByKey('water_mark_text');
		return $value ? $value : '';
	}

	/**
	 * 获取“水印放置位置”
	 * @return integer
	 */
	public static function getWaterMarkPosition()
	{
		$value = self::getValueByKey('water_mark_position');
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“水印融合度”
	 * @return integer
	 */
	public static function getWaterMarkPct()
	{
		$value = self::getValueByKey('water_mark_pct');
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“SMTP服务器”
	 * @return string
	 */
	public static function getSmtpHost()
	{
		$value = self::getValueByKey('smtp_host');
		return $value ? $value : '';
	}

	/**
	 * 获取“SMTP服务器端口”
	 * @return integer
	 */
	public static function getSmtpPort()
	{
		$value = self::getValueByKey('smtp_port');
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“SMTP服务器的账号”
	 * @return string
	 */
	public static function getSmtpUsername()
	{
		$value = self::getValueByKey('smtp_username');
		return $value ? $value : '';
	}

	/**
	 * 获取“SMTP服务器的密码”
	 * @return string
	 */
	public static function getSmtpPassword()
	{
		$value = self::getValueByKey('smtp_password');
		return $value ? $value : '';
	}

	/**
	 * 获取“管理员邮箱”
	 * @return string
	 */
	public static function getSmtpFrommail()
	{
		$value = self::getValueByKey('smtp_frommail');
		return $value ? $value : '';
	}

	/**
	 * 获取“文档列表每页展示条数”
	 * @return integer
	 */
	public static function getListRowsPosts()
	{
		$value = self::getValueByKey('list_rows_posts');
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“文档评论列表每页展示条数”
	 * @return integer
	 */
	public static function getListRowsPostComments()
	{
		$value = self::getValueByKey('list_rows_post_comments');
		return $value ? (int) $value : 0;
	}

	/**
	 * 获取“用户列表每页展示条数”
	 * @return integer
	 */
	public static function getListRowsUsers()
	{
		$value = self::getValueByKey('list_rows_users');
		return $value ? (int) $value : 0;
	}

}
