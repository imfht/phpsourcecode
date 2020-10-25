<?php
/**
 * 所属项目 110.
 * 开发者: 陈一枭
 * 创建日期: 2014-11-18
 * 创建时间: 10:09
 * 版权所有 想天软件工作室(www.ourstu.com)
 */

namespace Admin\Controller;


use Admin\Builder\AdminListBuilder;
use Think\Controller;

class ModuleController extends Controller
{
    protected $moduleModel;

    function _initialize()
    {
        $this->moduleModel = D('Module');
    }


    public function lists()
    {

        $listBuilder = new AdminListBuilder();


        $modules = $this->moduleModel->getAll();

        foreach ($modules as &$m) {
            if ($m['is_setup']) {
                $m['name'] = '<a style="color: green;font-weight: bold">√</a>' . $m['name'];
                if ($m['can_uninstall'])
                    $m['do'] = '<a class="btn btn-error "  onclick="moduleManager.uninstall(\'' . $m['id'] . '\')"><span style="color: white;font-weight: bold">×</span> 卸载</a>';
            } else {
                $m['name'] = '<span style="color: red;font-weight: bold">×</span>' . $m['name'];
                $m['do'] = '<a class="btn" onclick="moduleManager.install(\'' . $m['id'] . '\')"><span style="color: green;font-weight: bold">√</span> 安装</a>';
            }
            if($m['is_com']){
                $m['is_com']='<strong style="color: orange">商业模块</strong>';
            }else{
                $m['is_com']='<strong style="color: green">免费模块</strong>';
            }
        }
        unset($m);

        $listBuilder->data($modules);
        $listBuilder->title('模块管理');


        $listBuilder->keyId()->keyText('name', '模块名')->keyText('alias', '模块中文名')->keyText('summary', '模块介绍')
            ->keyText('version', '版本号')->keyText('is_com', '商业模块')
            ->keyLink('developer', '开发者', '{$website}')->keyText('entry', '前台入口')
            ->keyText('do', '操作');
        $listBuilder->display();
    }

    public function uninstall()
    {
        $aId = I('post.id', 0, 'intval');
        $res = $this->moduleModel->uninstall($aId);
        if ($res === true) {
            $this->success('卸载模块成功。', 'refresh');
        } else {
            $this->error('卸载模块失败。' . $res['error_code']);
        }


    }

    public function install()
    {
        $aId = I('post.id', 0, 'intval');
        $res = $this->moduleModel->install($aId);
        if ($res === true) {
            $this->success('安装模块成功。', 'refresh');
        } else {
            $this->error('安装模块失败。' . $res['error_code']);
        }


    }

} 