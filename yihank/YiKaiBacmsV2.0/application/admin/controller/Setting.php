<?php
namespace app\admin\controller;
use think\Lang;
/**
 * 网站设置
 */

class Setting extends Admin {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '网站设置',
                'description' => '设置网站整体功能',
                ),
            'menu' => array(
                    array(
                        'name' => '站点信息',
                        'url' => url('Setting/site'),
                        'icon' => 'exclamation-circle',
                    ),
                    array(
                        'name' => '模板设置',
                        'url' => url('Setting/tpl'),
                        'icon' => 'eye',
                    ),
                    array(
                        'name' => '手机设置',
                        'url' => url('Setting/mobile'),
                        'icon' => 'mobile',
                    ),
                /*array(
                   'name' => '性能设置',
                   'url' => url('Setting/performance'),
                   'icon' => 'dashboard',
               ),
              array(
                   'name' => '上传设置',
                   'url' => url('Setting/upload'),
                   'icon' => 'upload',
               )*/
                )
        );
    }
	/**
     * 站点设置
     */
    public function site(){
        if (input('post.')){
            if(model('Config')->edit()){
                return ajaxReturn(200,'站点配置成功！');
            }else{
                return ajaxReturn(0,'站点配置失败！');
            }
        }else{
            $this->assign('info',model('Config')->getInfo());
            return $this->fetch();
        }
    }
    /**
     * 手机设置
     */
    public function mobile(){
        if (input('post.')){
            if(model('Config')->edit()){
                return ajaxReturn(200,'模板配置成功！');
            }else{
                return ajaxReturn(0,'模板配置失败！');
            }
        }else{
            $this->assign('themesList',model('Config')->themesList());
            $this->assign('info',model('Config')->getInfo());
            return $this->fetch();
        }
    }
    /**
     * 模板设置
     */
    public function tpl(){
        if (input('post.')){
            if(model('Config')->edit()){
                return ajaxReturn(200,'模板配置成功！');
            }else{
                return ajaxReturn(0,'模板配置失败！');
            }
        }else{
            $this->assign('themesList',model('Config')->themesList());
            $this->assign('tplList',model('Config')->tplList());
            $this->assign('info',model('Config')->getInfo());
            return $this->fetch();
        }
    }
}

