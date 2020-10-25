<?php
namespace app\test_app\controller;

use app\common\logic\EbaLogic;
use app\common\model\eba\Eba;
use think\Cache;
use think\Db;
use think\Controller;

/**
 * 临时测试控制器
 * Class Test
 * @package app\test_app\controller
 */
class Test extends Controller {
    public function index() {
        $result = EbaLogic::is_exist('杭州宝井钢材加工配送有限公司', 'N');
        var_dump($result);
    }

}
