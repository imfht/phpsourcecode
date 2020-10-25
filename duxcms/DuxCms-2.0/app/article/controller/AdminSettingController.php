<?php
namespace app\article\controller;
use app\admin\controller\AdminController;
/**
 * 应用设置
 */
class AdminSettingController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        $menu = target('admin/Functions','controller');
        $menu = $menu->infoModule;
        return $menu;
        
    }

    /**
     * 设置
     */
    public function index(){
        if(!IS_POST){
            $breadCrumb = array('应用列表'=>url('admin/Functions/index'),'文章模块设置'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',current_config());
            $this->adminDisplay();
        }else{
            $file = 'article/config';
            if(save_config($file, $_POST)){
                $this->success('应用配置成功！');
            }else{
                $this->error('应用配置失败');
            }
        }
    }
    

}

