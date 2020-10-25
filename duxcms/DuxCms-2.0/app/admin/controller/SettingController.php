<?php
namespace app\admin\controller;
use app\admin\controller\AdminController;
/**
 * 网站设置
 */

class SettingController extends AdminController {

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
                        'name' => '手机设置',
                        'url' => url('Setting/mobile'),
                        'icon' => 'mobile',
                    ),
                    array(
                        'name' => '模板设置',
                        'url' => url('Setting/tpl'),
                        'icon' => 'eye',
                    ),
                    array(
                        'name' => '性能设置',
                        'url' => url('Setting/performance'),
                        'icon' => 'dashboard',
                    ),
                    array(
                        'name' => '上传设置',
                        'url' => url('Setting/upload'),
                        'icon' => 'upload',
                    )
                )
        );
    }
	/**
     * 站点设置
     */
    public function site(){
        if(!IS_POST){
            $breadCrumb = array('站点信息'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',target('Config')->getInfo());
            $this->adminDisplay();
        }else{
            
            if(target('Config')->saveData()){
                $this->success('站点配置成功！');
            }else{
                $this->error('站点配置失败');
            }
        }
    }
    /**
     * 手机设置
     */
    public function mobile(){
        if(!IS_POST){
            $breadCrumb = array('模板设置'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('themesList',target('Config')->themesList());
            $this->assign('tplList',target('Config')->tplList());
            $this->assign('info',target('Config')->getInfo());
            $this->adminDisplay();
        }else{
            if(target('Config')->saveData()){
                $this->success('模板配置成功！');
            }else{
                $this->error('模板配置失败');
            }
        }
    }
    /**
     * 模板设置
     */
    public function tpl(){
        if(!IS_POST){
            $breadCrumb = array('模板设置'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('themesList',target('Config')->themesList());
            $this->assign('tplList',target('Config')->tplList());
            $this->assign('info',target('Config')->getInfo());
            $this->adminDisplay();
        }else{
            if(target('Config')->saveData()){
                $this->success('模板配置成功！');
            }else{
                $this->error('模板配置失败');
            }
        }
    }
    /**
     * 性能设置
     */
    public function performance(){
        $file = CONFIG_PATH . 'performance.php';
        if(!IS_POST){
            $breadCrumb = array('性能设置'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',load_config($file));
            $this->adminDisplay();
        }else{
            if(save_config($file, $_POST)){
                $this->success('性能配置成功！');
            }else{
                $this->error('性能配置失败');
            }
        }
    }
    /**
     * 上传设置
     */
    public function upload(){
        $file = CONFIG_PATH . 'upload.php';
        if(!IS_POST){
            $breadCrumb = array('上传设置'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',load_config($file));
            $this->adminDisplay();
        }else{
            if(save_config($file, $_POST)){
                $this->success('上传配置成功！');
            }else{
                $this->error('上传配置失败');
            }
        }
    }
}

