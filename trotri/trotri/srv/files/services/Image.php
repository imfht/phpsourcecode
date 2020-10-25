<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace files\services;

use tfc\util\String;
use tfc\util\Image AS ImageManager;
use tfc\saf\Cfg;
use system\services\DataOptions;
use system\services\Options;

/**
 * Image class file
 * 业务层：业务处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Image.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Image
{
	/**
	 * 生成验证码，待完善
	 * @return void
	 */
	public static function verify()
	{
		$fontFile = Cfg::getApp('fontfile');
		$chars = String::randStr(4);
		$width = 140;
		$height = 40;

		ImageManager::verify($fontFile, $chars, $width, $height);
	}

	/**
	 * 生成缩略图
	 * @param string $source
	 * @param string $toPath
	 * @return string|false
	 */
	public static function thumbnail($source, $toPath = null)
	{
		$thumbWidth = Options::getThumbWidth();
		$thumbHeight = Options::getThumbHeight();
		if ($thumbWidth > 0 && $thumbHeight > 0) {
			if ($toPath === null) {
				$toPath = dirname($source) . DS . 'thumb_' . basename($source);
			}

			if (ImageManager::thumbnail($source, $thumbWidth, $thumbHeight, $toPath)) {
				return $toPath;
			}
		}

		return false;
	}

	/**
	 * 生成文字水印
	 * @param string $source
	 * @param string $toPath
	 * @return string|false
	 */
	public static function water($source, $toPath = null)
	{
		$type = Options::getWaterMarkType();
		if ($type !== DataOptions::WATER_MARK_TYPE_IMGDIR && $type !== DataOptions::WATER_MARK_TYPE_TEXT) {
			return false;
		}

		$position = Options::getWaterMarkPosition();
		if ($position < 1 || $position > 9) {
			return false;
		}

		$offset = 1;

		if ($toPath === null) {
			$toPath = dirname($source) . DS . 'water_' . basename($source);
		}

		if ($type === DataOptions::WATER_MARK_TYPE_TEXT) {
			$text = Options::getWaterMarkText();
			if ($text !== '') {
				$fontFile = Cfg::getApp('fontfile');
				if (ImageManager::textWater($source, $text, $fontFile, $toPath, $position, $offset)) {
					return $toPath;
				}
			}
		}
		elseif ($type === DataOptions::WATER_MARK_TYPE_IMGDIR) {
			$water = Options::getWaterMarkImgdir();
			if ($water !== '') {
				$pct = max(Options::getWaterMarkPct(), 0);
				if (ImageManager::imageWater($source, $water, $toPath, $position, $offset, $pct)) {
					return $toPath;
				}
			}
		}

		return false;
	}

	/**
	 * 获取图片详情
	 * @param string $fileName
	 * @return array
	 */
	public static function imgStat($fileName)
	{
		$ret = array();

		if (is_file($fileName)) {
			$pathinfo = pathinfo($fileName);
			$stat = stat($fileName);
			$size = getimagesize($fileName);

			$ret = array(
				'directory' => $pathinfo['dirname'],   // 目录名
				'basename'  => $pathinfo['basename'],  // 文件名+扩展名
				'filename'  => $pathinfo['filename'],  // 文件名
				'extension' => $pathinfo['extension'], // 扩展名
				'filesize'  => $stat['size'],          // 文件大小
				'atime'     => $stat['atime'],         // 上次访问时间
				'mtime'     => $stat['mtime'],         // 上次修改时间
				'ctime'     => $stat['ctime'],         // 上次改变时间
				'width'     => $size[0],               // 图片宽
				'height'    => $size[1],               // 图片高
				'mime'      => $size['mime']           // 图片类型
			);
		}

		return $ret;
	}
}
