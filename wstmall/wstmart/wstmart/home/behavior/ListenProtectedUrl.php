<?php
namespace wstmart\home\behavior;
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
 * 检测用户有没有登录和访问权限
 */
class ListenProtectedUrl 
{
    public function run($params){
	    $request = request();
	    $urls = WSTConf('protectedUrl');
        $visit = strtolower($request->module()."/".$request->controller()."/".$request->action());

        //受保护资源进来检测身份
        if(isset($urls[$visit])){
        	$menuType = (int)$urls[$visit];
        	$userType = -1;
			$shopId = (int)session('WST_USER.shopId');
        	if((int)session('WST_USER.userId')>0)$userType = 0;
        	if($shopId>0)$userType = 1;

        	//未登录不允许访问受保护的资源
        	if($userType==-1){
		        if($request->isAjax()){
		        	echo json_encode(['status'=>-999,'msg'=>'对不起，您还没有登录，请先登录']);
		        }else{
		        	header("Location:".url('home/users/login'));
		        }
		        exit();
        	}else if($userType==1){
                $puser = session('WST_USER');
                if(isset($puser['shopId'])){
                    $shop = model('Shops')->getFieldsById($shopId,'shopStatus');
                    if(empty($shop) || $shop['shopStatus']==-1){
                        model('Users')->logout();
                        if($request->isAjax()){
                            echo json_encode(['status'=>-999,'msg'=>'对不起，您的店铺已停用']);
                        }else{
                            header("Location:".url('shop/index/login'));
                        }
                        exit();
                    }
				}
			}else{
				//已登录但不是商家 则不允许访问受保护的商家资源
				if($menuType==1){
					if($request->isAjax()){
						echo json_encode(['status'=>-999,'msg'=>'对不起，您不是商家，请先申请为商家再访问']);
					}else{
						header("Location:".url('shop/index/login'));
					}
					exit();
				}
        	}
            //商家角色权限
            if(((int)session('WST_USER.roleId')>0) && $userType==1 && $menuType==1){
                $shopMenuUrls = model('HomeMenus')->getShopMenusUrl();
                if(!in_array($visit,$shopMenuUrls)){
                    header("Location:".url('home/error/index'));
                    exit();
                }
            }
        }
    }
}