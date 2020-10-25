<?php
/**
 * 系统管理
 */
namespace Control\Controller;
use Core\Model\Site;
use Think\Controller;
use Think\Storage;

class DatabaseController extends Controller {
    public function _empty() {
        $this->display('backup');
    }
}