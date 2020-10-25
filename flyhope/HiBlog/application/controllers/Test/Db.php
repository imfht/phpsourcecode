<?php

/**
 * 数据库测试类
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Test_DbController extends \Yaf_Controller_Abstract {
    
    
    public function indexAction() {
        
        //生产环境禁止回调
        return false;
        
        $action = \Comm\Arg::get('action', FILTER_DEFAULT, null, true);
        switch($action) {
            
            //测试查询
            case 'select' :
                $db = new \Comm\Db\Simple('test');
                $result = $db->limit(10)->fetchAll();
                print_r($result);
                break;
            case 'config' :
                print_r(\Model\Config::showAll());
                break;
        }
        return false;
    }
    
} 
