<?php namespace qeephp\tools\getvideo;

abstract class Video
{
	
	public $id;#视频的ID，用来在视频网站查询视频
	public $html_url;#视频的源url
	public $cover;# 视频网站提供的截图或封面
	public $title;#视频的标题
	public $flash;#视频flash格式
	public $m3u8;

	protected function json($rearr=false)
	{
		$arr = array(
				'url'=> $this->html_url,
				'id'=> $this->id,
		        'cover' => $this->cover,
		        'title' => $this->title,
		        'm3u8'  => $this->m3u8,
		        'flash' => $this->flash,
		    );    
		return $rearr ? $arr : json_encode($arr,JSON_NUMERIC_CHECK);
	}

	/**
	 * 解析url并返回结果对象
	 * 
	 * @return array
	 */
	abstract protected function compiler($url);

	/**
	 * 解析 URL 并返回对应结果
	 * 
	 * @param string $url
	 * 
	 * @return array
	 */
	static function parse($url)
	{
		static $patterns = array(
				'youku' =>	'/youku/',
				'tudou' =>	'/tudou/',
				'iqiyi' =>	'/iqiyi/',
				'sohu' =>	'/sohu/',
				'56' =>	'/56\.com/',
				'ku6' =>	'/ku6/',
				'youtube' =>	'/youtube/',
				'sina' =>	'/(iask|sina)/',
			);

		$clazz = false;
		foreach ($patterns as $key => $pattern)
		{
			if (preg_match($pattern, $url))
			{
				$clazz = $key;
				break;
			}
		}
//dump($clazz);
		if ( 'youku' == $clazz )
		{
			$obj = new Youku();
			return $obj->compiler($url);
		}

		return false;
	}

}