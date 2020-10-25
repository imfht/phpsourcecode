<?php
namespace Home\Controller;
use Think\Controller;

/**
* 创翔科技微信
*/
class CxkjController extends Controller
{
	
	public function index()
	{
		$appid = 'wxa0b50a7f09c334a6';
        $appsecret = 'e1ef912bc35ceff03842a21fd82d4b94';
        $menu = array( //菜单
            'button' => array(
                //一级菜单
                array(
                    'name' => urlencode('个人信息'),
                    'sub_button' => array(//二级菜单
                        array(
                            "type" => "view",
                            "url" => urlencode("http://yanda.net.cn/resume1.html"),
                            'name' => urlencode('个人简历'),
                        ),
                        array(
                            "type" => "CLICK",
                            "key" => 'COMP_PROJ',
                            'name' => urlencode('企业项目'),
                        ),
                        array(
                            "type" => "view",
                            "url" => urlencode("http://git.oschina.net/yansongda"),
                            'name' => urlencode('个人项目'),
                        ),
                    ),
                ),
                array(
                    'name' => urlencode('我要扫码'),
                    'sub_button' => array(//二级菜单
                        array(
                            "type" => "scancode_waitmsg",
                            "key" => 'rselfmenu_0_0',
                            'name' => urlencode('扫码带提示'),
                        ),
                        array(
                            "type" => "scancode_push",
                            "key" => 'rselfmenu_0_1',
                            'name' => urlencode('扫码推事件'),
                        ),
                    ),
                ),
                array(
                    'name' => urlencode('我要发图'),
                    'sub_button' => array(//二级菜单
                        array(
                            "type" => "pic_sysphoto",
                            "key" => 'rselfmenu_1_0',
                            'name' => urlencode('系统拍照发图'),
                        ),
                        array(
                            "type" => "pic_photo_or_album",
                            "key" => 'rselfmenu_1_1',
                            'name' => urlencode('拍照或者相册发图'),
                        ),
                        array(
                            "type" => "pic_weixin",
                            "key" => 'rselfmenu_1_2',
                            'name' => urlencode('微信相册发图'),
                        ),
                    ),
                ),
            ),
        );
        
        $weixin = new \Common\Lib\Weixin\Weixin($appid, $appsecret);
        $weixin->creatMenu($menu);
        $typeData = $weixin->getTypedata();
        $typeData['met'] = 'cxkj';
        $this->assign('data', $typeData);
        $this->display('Index/index');
	}
}