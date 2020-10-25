<?php
/**
 * 内存缓存信赖关系
 *
 * @author		黑冰 <001.black.ice@gmail.com>
 * @copyright	Copyright 2014
 * @package		com.tools
 */

class CMemCacheDependency extends CCacheDependency
{
    public $mem_key;

	public function __construct($mem_key = null)
    {
        $this->mem_key  = $mem_key;
	}

	protected function generateDependentData()
    {
        print_r($this);
        $value = Yii::app()->cache->get($this->mem_key);
        echo $value.'**';
        return time();
    }
}

