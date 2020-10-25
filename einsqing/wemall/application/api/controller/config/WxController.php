<?php
namespace app\api\controller\config;
use app\api\controller\BaseController;

class WxController extends BaseController
{
	//分享配置参数前台要用encodeURIComponent(location.href.split('#')[0])
	public function getJsSign(){
	    $url = urldecode(input('param.url'));

		$jsSign = model('WxConfig')->getJsSign($url);

		$data['jsSign'] = $jsSign;
		return json(['data' => $data, 'msg' => '分享配置', 'code' => 1]);
	}
}