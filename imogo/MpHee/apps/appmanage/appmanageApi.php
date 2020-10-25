<?php
class appmanageApi extends baseApi{
  
  public function getMenu(){
		return array(
					'sort'=>1,
					'title'=>'应用管理',
					'list'=>array(
						'我的应用'=>url('index/index'),
						'应用商城'=>'http://appstore.mphee.com/index.php?domain='. $_SERVER['HTTP_HOST']. '&rooturl='.urlencode( __ROOT__ ).'/',
						'MpHee云平台'=>'http://appstore.mphee.com/index.php?r=default/index/addwebsite&domain='. $_SERVER['HTTP_HOST']. '&rooturl='.urlencode( __ROOT__.'/' ).'&clientver='.getVer(),
					)
			);
	} 
}