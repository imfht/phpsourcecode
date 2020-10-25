<?php
/**
 * 会话处理
 */
namespace App\Behavior;
use Core\Model\Site;

class AuthBehavior {
    public function run(&$params) {
        //session('__:uid', 1);
        
        //#debug
        Site::loadSettings();
    }
}