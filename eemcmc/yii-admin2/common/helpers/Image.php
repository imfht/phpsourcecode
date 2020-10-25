<?php

namespace common\helpers;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;

/**
 * 图片服务辅助类
 * 
 * @author ken <vb2005xu@qq.com>
 */
class Image extends \yii\imagine\BaseImage
{

	/**
	 * @inheritdoc
	 */
	public static function thumbnail($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET)
	{

		$img = static::getImagine()->open(\Yii::getAlias($filename));

		//判断宽高参数
		if (empty($width) && empty($height))
		{
			throw new \Exception('必须指定需要裁切的宽度或高度');
		}
		elseif (empty($width))
		{
			$width = ceil(doubleval($height) / doubleval($img->getSize()->getHeight()) * doubleval($img->getSize()->getWidth()));
		}
		elseif (empty($height))
		{
			
			$height = ceil(doubleval($width) / doubleval($img->getSize()->getWidth()) * doubleval($img->getSize()->getHeight()));
		}

		$box = new Box($width, $height);
		if (($img->getSize()->getWidth() <= $box->getWidth() && $img->getSize()->getHeight() <= $box->getHeight()) || (!$box->getWidth() && !$box->getHeight()))
		{
			return $img->copy();
		}

		$img = $img->thumbnail($box, $mode);

		// create empty image to preserve aspect ratio of thumbnail
		$thumb = static::getImagine()->create($box, new Color('FFF', 100));

		// calculate points
		$size = $img->getSize();

		$startX = 0;
		$startY = 0;
		if ($size->getWidth() < $width)
		{
			$startX = ceil($width - $size->getWidth()) / 2;
		}
		if ($size->getHeight() < $height)
		{
			$startY = ceil($height - $size->getHeight()) / 2;
		}

		$thumb->paste($img, new Point($startX, $startY));

		return $img;
	}

}
