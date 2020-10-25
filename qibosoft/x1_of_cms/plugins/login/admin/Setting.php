<?php
namespace plugins\login\admin;

use app\common\controller\admin\Setting AS _Setting;


class Setting extends _Setting
{    
    /**
     * 参数设置
     * {@inheritDoc}
     * @see \app\common\controller\admin\Setting::index()
     */
    public function index($group=null){
        return parent::index($group);
    }
}

