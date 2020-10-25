<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 站点安全
 */

class AdminSafeController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '安全中心',
                'description' => '测试站点整体安全性',
                ),
            );
    }
	/**
     * 安全测试
     */
    public function index(){
        
        $checkArray = array();
        //上传目录检测
        $dir = @fopen(__ROOT__ .'/upload','wb');
        if ($dir !== false)
        {
            $checkArray['upload'] = true;
        }
        $breadCrumb = array('安全中心'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('safeArray',target('Safe')->getList());
        $this->assign('checkArray',$checkArray);
        $this->adminDisplay();
    }

}

