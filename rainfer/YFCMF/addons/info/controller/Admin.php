<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace addons\info\controller;

use app\common\controller\Base;
use app\common\model\Addon as AddonModel;
use app\common\widget\Widget;

class Admin extends Base
{
    protected function initialize()
    {
        //调用admin/Base控制器的初始化
        action('admin/Base/initialize');
    }

    /*
     * 设置
     * @return mixed
     */
    public function config()
    {
        $model = new AddonModel;
        if (request()->isAjax()) {
            $display = input('display', 0, 'intval');
            $rst     = $model->setConfig('info.display', $display);
            if ($rst) {
                $this->success('更新设置成功', 'admin/Addons/addonsIndex', ['is_frame' => 1]);
            } else {
                $this->error('更新设置失败', 'admin/Addons/addonsIndex', ['is_frame' => 1]);
            }
        } else {
            $config = $this->getConfig('info');
            $widget = new Widget();
            return $widget
                ->addItems([['radio', 'display', '是否显示', ['1' => '显示', '0' => '不显示'], isset($config['display']) ? $config['display'] : 1]])
                ->setUrl(addon_url('info://Admin/config'))
                ->setAjax('ajaxForm-noJump')
                ->fetch();
        }
    }
}
