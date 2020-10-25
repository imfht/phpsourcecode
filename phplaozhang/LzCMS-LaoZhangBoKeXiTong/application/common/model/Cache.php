<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class Cache extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}

	//更新网站设置缓存
	function setting(){
		$settings = db('setting')->column('key,value');
		return	cache('settings', $settings);
	}

	//更新缓存
	function update_cache(){
		delDir(TEMP_PATH);
		delDir(CACHE_PATH);
		model('common/setting')->cache_setting();
		model('common/category')->cache_models(); 
		model('common/category')->cache_category();
		return true;
	}


	
}