<?php
/**
 * 临时调试控制器
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Test_DebugController extends \Yaf_Controller_Abstract {


    public function indexAction() {

        $string = '<p>中华人<b>民</b>共和国</p>';
        $width = 3;
        echo Comm\Str::truncateSummary($string, $width);
                
    }

}
