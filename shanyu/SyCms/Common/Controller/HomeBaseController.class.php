<?php
namespace Common\Controller;
use Think\Controller;
class HomeBaseController extends Controller {
    public function _initialize() {
        //加载Home配置
        $this->initConfig();
    }

    //加载动态配置
    private function initConfig(){
        $home_config = F('HomeConfig');
        if(!$home_config){
            $home_config=D('Common/Config')->getConfig('Home');
            F('HomeConfig',$home_config);
        }

        //模板样式
        $home_config['TMPL_PARSE_STRING']=array(
            '__THEME__' => __ROOT__.'/Public/Home/'.$home_config['DEFAULT_THEME'],
        );
        C($home_config);
    }

    public function toMobile($url){
        //判断是否为手机访问
        $Mobile=new \Lib\Util\MobileDetect();
        if($Mobile->isMobile()) {

            // $route=basename(__SELF__,".html");
            // $route_rule=F('RouteRule');
            // $rule=str_replace('home', 'mobile', $route_rule[$name]);
            header('Location:'.U('Mobile/'.$url));
            exit;
        }
    }


}