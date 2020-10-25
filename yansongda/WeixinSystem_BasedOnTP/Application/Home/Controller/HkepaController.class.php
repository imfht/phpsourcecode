<?php
namespace Home\Controller;
use Think\Controller;

/**
* 环协微信
*/
class HkepaController extends Controller
{
	
	public function index()
    {
        $appid = '';
        $appsecret = '';
        $menu = array( //菜单
            'button' => array(
                //一级菜单
                array(
                    'name' => urlencode('个人信息'),
                    'sub_button' => array(//二级菜单
                        array(
                            "type" => "view",
                            "url" => urlencode("http://yanda.net.cn/"),
                            'name' => urlencode('网站'),
                        ),
                        array(
                            "type" => "view",
                            "url" => urlencode("http://www.youku.com/"),
                            'name' => urlencode('视频'),
                        ),
                    ),
                ),
                array(
                    'name' => urlencode('放松下'),
                    'type' => 'click',
                    'key' => 'V1001_TODAY_MUSIC',
                ),
                array(
                    'name' => urlencode('项目'),
                    'sub_button' => array(//二级菜单
                        array(
                            "type" => "view",
                            "url" => urlencode("http://yanda.net.cn/"),
                            'name' => urlencode('环协网络建设'),
                        ),
                        array(
                            "type" => "view",
                            "url" => urlencode("http://yanda.net.cn/"),
                            'name' => urlencode('唯尔易购'),
                        ),
                    ),
                ),
            ),
        );
        
        $weixin = new \Common\Lib\Weixin\Weixin($appid, $appsecret);
        //$weixin->creatMenu($menu);
        $typeData = $weixin->getTypedata();
        $typeData['met'] = 'hkepa';
        $this->assign('data', $typeData);
        $this->display('Index/index');
    }
}