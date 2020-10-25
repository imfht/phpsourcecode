<?php 
/**
*	小米推送
*	参考的开发：https://dev.mi.com/console/doc/detail?pId=1163
*/
class xmpushChajian extends Chajian{
	
	private $api_url 	 = 'https://api.xmpush.xiaomi.com/v2/message/alias';
	private $api_urltest = 'https://sandbox.xmpush.xiaomi.com/v2/message/alias';
	
	
	//安卓的
	private $android_secret 	= ''; //安卓小米的secret
	private $android_package 	= ''; //包名
	
	//IOS苹果
	private $ios_secret 		= ''; //IOS的secret
	private $ios_bundleid 		= '';

	
	protected function initChajian()
	{
	}
	
	public function sendbool()
	{
		if($this->android_secret=='')return false;
		return true;
	}

	/**
	*	安卓推送通知
	*/
	public function androidsend($alias, $title, $cont, $payload='')
	{
		if(!$alias)return false;
		if(is_array($alias))$alias = join(',', $alias);
		$data = array(
			'payload' => '',
			'restricted_package_name' => $this->android_package,
			'pass_through' => '0',   // 0 表示通知栏消息1表示透传消息
			'title' => $title,
			'description' => $cont,
			'alias' => $alias,
			'notify_type' => '1', //提示语就好了
			'notify_id' => rand(1,45), //可多条显示
			'extra.notify_foreground' => '1',
			'extra.notify_effect' => '1',
		);
		if($payload)$data['payload'] = urlencode($payload);
		return c('curl')->postcurl($this->api_url, $data, 0, array(
			'Authorization'=> 'key='.$this->android_secret
		));
	}
	
	public function iossend($alias, $title, $cont)
	{
		if(!$alias || $this->ios_secret=='')return false;
		if(is_array($alias))$alias = join(',', $alias);
		$data = array(
			'title' => $title,
			'aps_proper_fields.title' => $title,
			'description' => $cont,
			'aps_proper_fields.body' => $cont,
			'alias' => $alias,
			'extra.badge'=>'1'
		);
		return c('curl')->postcurl($this->api_url, $data, 0, array(
			'Authorization'=> 'key='.$this->ios_secret
		));
	}
}