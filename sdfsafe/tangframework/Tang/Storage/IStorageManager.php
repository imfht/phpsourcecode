<?php
namespace Tang\Storage;
use Tang\Manager\IManager;

interface IStorageManager extends IManager
{
	/**
	 * 获取URL地址
	 * @param $info
	 * @return string
	 */
	public function getUrl($info);
}