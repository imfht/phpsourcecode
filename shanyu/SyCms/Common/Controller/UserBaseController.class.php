<?php
namespace Common\Controller;
use Think\Controller;
class UserBaseController extends Controller {
    public function _initialize() {
        $this->initConfig();

        $uid=safe_cookie('uid');
        if(is_numeric($uid)) define(UID, $uid);
        else $this->redirect('User/Public/login');

        if(IS_GET){
            $this->initMenu();
        }
    }

    //加载动态配置
    private function initConfig(){
        $home_config = F('HomeConfig');
        if(!$home_config){
            $home_config=D('Common/Config')->getConfig('Home');
            F('HomeConfig',$home_config);
        }

        $home_config['DEFAULT_THEME']='';
        C($home_config);
    }

    //会员中心菜单
    private function initMenu(){
        $menu=include MODULE_PATH.'conf/menu.php';
        $nav_top=$menu[0];
        unset($menu[0]);

        $now=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        foreach ($menu as $_k => $_v) {
            //1级点击
            if(array_key_exists($now, $_v)){
                $nav_left=$_v;
                //面包屑导航
                $nav_line[]=$nav_top[$_k-1][0];
                $nav_line[]=$nav_left[$now][0];
                //导航高亮
                $nav_top[$_k-1][3]='on';
                $nav_left[$now][3]='on';

                break;
            }
            //0级点击
            foreach ($_v as $_kk => $_vv) {
                if(!isset($_vv[2])) continue;
                if(array_key_exists($now,$_vv[2])){
                    $nav_left=$_v;
                    //面包屑导航
                    $nav_line[]=$nav_top[$_k-1][0];
                    $nav_line[]=array($nav_left[$_kk][0],key($nav_left[$_kk][0]));
                    $nav_line[]=$_vv[2][$now][0];

                    //导航高亮
                    $nav_top[$_k-1][3]='on';
                    $nav_left[$_kk][3]='on';

                    // die;
                    break;
                }
            }

        }
        $this->assign('nav_line',$nav_line);
        $this->assign('nav_top',$nav_top);
        $this->assign('nav_left',$nav_left);
    }


}