<?php
namespace app\common\model;

use think\Model;

/**
* 站点设置模型类
*/
class Setting extends Model
{
	
	function initialize()
	{
		parent::initialize();
	}

	/**
	 * 编辑
	 * @param array 	$params 内容
	 * @return [boolean]
	 */
	public function update_setting($params){
		$settings = $this->get_setting();
		foreach ($params as $key => $value) {
			if (array_key_exists ($key,$settings) === true) {
				$this->isUpdate(true)->where(array('key' => $key))->update(array('value' => $value));
			} else {
				$this->insert(array('key' => $key ,'value' => $value));
			}
		}
		$this->cache_setting();
		return TRUE;
	}

	/**
	 * 获取后台设置
	 * @param  string 	$key 缓存名称(为空取所有设置)
	 * @return [result]
	 */
	public function get_setting($key=false){
		if(cache('settings')){
			$settings = cache('settings');
		}else{
			$this->cache_setting();
			$settings = cache('settings');
		}
		if(is_string($key)) return $settings[$key];
		return $settings;
	}

	/**
	 * 生成sitemap
	 * @param  string 	$changefreq 更新频率
	 * @return [result]
	 */
	public function set_sitemap($changefreq,$model_ids = '2'){
		$site_url = $this->get_setting('site_url');
		if(empty($site_url)){
			$site_url = request()->domain();
		}
		$site_url = trim($site_url,'/').'/';
		$model_ids_arr = explode(',', $model_ids);
		$models = cache('models');

		$sitemap_str  = '<?xml version="1.0" encoding="UTF-8"?>';
		$sitemap_str .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$sitemap_str .= '<url>';
     	$sitemap_str .= '<loc>'.$site_url.'</loc>';
      	$sitemap_str .= '<lastmod>'.date('Y-m-d').'</lastmod>';
      	$sitemap_str .= '<changefreq>daily</changefreq>';
      	$sitemap_str .= '<priority>1.0</priority>';
   		$sitemap_str .= '</url>';

   		foreach ($model_ids_arr as $model_id) {
   			$data = model('common/'.$models[$model_id]['tablename'])->order('id desc')->select();
	   		foreach ($data as $k => $v) {
	   			if(!$v['url']){ continue; }
	   			if(parse_url($v['url'])['host'] && (parse_url($v['url'])['host']) != (parse_url($site_url)['host'])){ continue; }
				if(!parse_url($v['url'])['host']){
					$v['url'] = trim($site_url,'/').$v['url'];
				}
	   			if(!isset($v['update_time']) || empty($v['update_time'])){ $v['update_time'] = $v['create_time']; }
	   			$sitemap_str .= '<url>';
		     	$sitemap_str .= '<loc>'.$v['url'].'</loc>';
		      	$sitemap_str .= '<lastmod>'.format_datetime($v['update_time'],1,'Y-m-d').'</lastmod>';
		      	$sitemap_str .= '<changefreq>'.$changefreq.'</changefreq>';
		      	$sitemap_str .= '<priority>0.8</priority>';
		   		$sitemap_str .= '</url>';
	   		}
   		}
	  
		$categorys = cache('categorys');
		foreach ($categorys as $k => $v) {
			if(!$k || !$v['url']){ continue; }
			if(parse_url($v['url'])['host'] && (parse_url($v['url'])['host']) != (parse_url($site_url)['host'])){ continue; }
			if(!parse_url($v['url'])['host']){
				$v['url'] = trim($site_url,'/').$v['url'];
			}
			$sitemap_str .= '<url>';
	     	$sitemap_str .= '<loc>'.$v['url'].'</loc>';
	      	$sitemap_str .= '<lastmod>'.date('Y-m-d').'</lastmod>';
	      	$sitemap_str .= '<changefreq>weekly</changefreq>';
	      	$sitemap_str .= '<priority>0.6</priority>';
	   		$sitemap_str .= '</url>';
		}
		$sitemap_str .= '</urlset>';
		return file_put_contents('Sitemap.xml', $sitemap_str);
	}

	//更新网站设置缓存
	function cache_setting(){
		$settings = db('setting')->column('key,value');
		return	cache('settings', $settings);
	}

}