<?php
namespace Common\Controller;
use Think\Controller;
class MobileBaseController extends Controller {
    public function _initialize() {

        $this->initConfig();
    }

    //加载动态配置
    private function initConfig(){
        $mobile_config = F('HomeConfig');
        if(!$mobile_config){
            $mobile_config=D('Common/Config')->getConfig('Home');
            F('HomeConfig',$mobile_config);
        }

        //模板样式
        $mobile_config['TMPL_PARSE_STRING']=array(
            '__THEME__' => __ROOT__.'/Public/Mobile/'.$mobile_config['DEFAULT_THEME'],
        );
        C($mobile_config);
    }


}