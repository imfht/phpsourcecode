<?php
namespace wstmart\home\controller;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 默认控制器
 */
class Index extends Base{
	protected $beforeActionList = [
          'checkAuth' =>  ['only'=>'getsysmessages']
    ];
    public function download(){
        return view('/download');
    }
    public function index(){
        $categorys = model('GoodsCats')->getFloors();
        $this->assign('floors',$categorys);
        $this->assign('hideCategory',1);
        //获取用户积分
        $rs = model('Users')->getFieldsById((int)session('WST_USER.userId'),'userScore');
        $this->assign('object',$rs);
        return $this->fetch('index');
    }
    /**
     * 检测伪静态
     */
    public function checkroute(){
        return WSTReturn('ok',1);
    }
    /**
     * 保存目录ID
     */
    public function getMenuSession(){
    	$menuId = input("post.menuId");
    	session('WST_MENUID3',$menuId);
    } 
    /**
     * 获取用户信息
     */
    public function getSysMessages(){
    	$rs = model('Systems')->getSysMessages();
    	return $rs;
    }
    /**
     * 定位菜单以及跳转页面
     */
    public function position(){
    	$menuId = (int)input("post.menuId");
    	$menuType = ((int)input("post.menuType")==1)?1:0;
        $menus = model('HomeMenus')->getParentId($menuId);
        session('WST_MENID'.$menus['menuType'],$menus['parentId']);
    	session('WST_MENUID3'.$menuType,$menuId);
    }

    /**
     * 转换url
     */
    public function transfor(){
        $data = input('param.');
        $url = $data['url'];
        unset($data['url']);
        echo Url($url,$data);
    }
    /**
     * 保存url
     */
    public function currenturl(){
    	session('WST_HO_CURRENTURL',input('url'));
    	return WSTReturn("", 1);
    }
}
