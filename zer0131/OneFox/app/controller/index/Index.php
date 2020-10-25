<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 默认控制器
 */
namespace controller\index;

use controller\Base;
use lib\Test\Test;

class Index extends Base {

    public $actions = [
        'test' => 'actions\\index\\Test',
    ];
    
    /**
     * 默认方法
     */
    public function indexAction(){
		$this->show();
    }

    public function serviceAction() {
        dumper('service');
    }
}
