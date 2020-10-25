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
use tfc\saf\Text;
use tfc\util\FileManager;
use files\services\Image;
use files\services\Upload;

/**
 * Pictures class file
 * 图片管理
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Pictures.php 1 2014-09-29 23:33:28Z Code Generator $
 * @package modules.system.model
 * @since 1.0
 */
class Pictures extends BaseModel
{
	/**
	 * @var string 上传图片保存目录
	 */
	protected $_directory = null;

	/**
	 * @var instance of tfc\util\FileManager
	 */
	protected $_fileManager = null;

	/**
	 * (non-PHPdoc)
	 * @see \library\BaseModel::_init()
	 */
	protected function _init()
	{
		parent::_init();

		$this->_directory = DIR_DATA_UPLOAD . DS . 'imgs';
		$this->_fileManager = new FileManager();

		$this->_fileManager->mkDir($this->_directory);
		$this->_fileManager->mkDir($this->_directory . DS . date('Ym'));
		$this->_fileManager->mkDir($this->_directory . DS . date('Ym') . DS . date('d'));
	}

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
			'directory' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_DIRECTORY_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_DIRECTORY_HINT'),
			),
			'file_count' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_FILE_COUNT_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_FILE_COUNT_HINT'),
			),
			'picture_preview' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_PICTURE_PREVIEW_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_PICTURE_PREVIEW_HINT'),
			),
			'picture_name' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_PICTURE_NAME_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_PICTURE_NAME_HINT'),
			),
			'picture_url' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_PICTURE_URL_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_PICTURE_URL_HINT'),
			),
			'file_size' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_FILE_SIZE_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_FILE_SIZE_HINT'),
			),
			'width_height' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_WIDTH_HEIGHT_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_WIDTH_HEIGHT_HINT'),
			),
			'dt_created' => array(
				'__tid__' => 'main',
				'type' => 'text',
				'label' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_DT_CREATED_LABEL'),
				'hint' => Text::_('MOD_SYSTEM_SYSTEM_PICTURES_DT_CREATED_HINT'),
			),
		);

		return $output;
	}

	/**
	 * 获取指定日期下的图片目录
	 * @param integer $Ym
	 * @return array
	 */
	public function getDirs($Ym = 0)
	{
		$data = array();

		$directory = $this->_directory;
		if (($Ym = (int) $Ym) > 0) {
			$directory .= DS . $Ym;
		}
		else {
			$Ym = '';
		}

		$dirs = $this->scanDir($directory);
		$dirs = array_reverse($dirs);
		foreach ($dirs as $fileName) {
			if (!$this->_fileManager->isDir($fileName)) {
				continue;
			}

			$dirName = pathinfo($fileName, PATHINFO_BASENAME);
			$fileCount = count($this->scanDir($fileName));

			$data[] = array(
				'directory' => $Ym . $dirName,
				'file_count' => $fileCount
			);
		}

		return $data;
	}

	/**
	 * 获取所有的图片文件
	 * @param integer $Ymd
	 * @return array
	 */
	public function getFiles($Ymd)
	{
		$data = array();

		if (($Ymd = (int) $Ymd) <= 0) {
			return $data;
		}

		$directory = $this->_directory . DS . substr($Ymd, 0, 6) . DS . substr($Ymd, 6);
		$fileNames = $this->scanDir($directory);
		$fileNames = array_reverse($fileNames);
		foreach ($fileNames as $fileName) {
			if (!is_file($fileName)) {
				continue;
			}

			$imgStat = Image::imgStat($fileName);

			$directory   = isset($imgStat['directory']) ? $imgStat['directory'] : '';
			$pictureName = isset($imgStat['basename'])  ? $imgStat['basename']  : '';
			$width       = isset($imgStat['width'])     ? $imgStat['width']     : 0;
			$height      = isset($imgStat['height'])    ? $imgStat['height']    : 0;
			$fileSize    = isset($imgStat['filesize'])  ? $imgStat['filesize'] / 1024  : 0;
			$dtCreated   = isset($imgStat['ctime'])     ? date('Y-m-d H:i:s', $imgStat['ctime']) : '';

			$pictureUrl  = Upload::getUrl($fileName);
			$fileSize    = round($fileSize, 2) . 'KB';

			$data[] = array(
				'directory'    => $directory,
				'picture_name' => $pictureName,
				'picture_url'    => $pictureUrl,
				'file_size'    => $fileSize,
				'width_height' => $width . '*' . $height,
				'dt_created'   => $dtCreated
			);
		}

		return $data;
	}

	/**
	 * 获取目录中的文件
	 * @param string $directory
	 * @return array
	 */
	public function scanDir($directory)
	{
		$data = array();

		$fileNames = $this->_fileManager->scanDir($directory);
		if (is_array($fileNames)) {
			foreach ($fileNames as $fileName) {
				if (pathinfo($fileName, PATHINFO_BASENAME) === 'index.html') {
					continue;
				}

				$data[] = $fileName;
			}
		}

		return $data;
	}
}
