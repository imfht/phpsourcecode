<?php
// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * 用户输入监测类
 +------------------------------------------------------------------------------
 */
class CheckInput {
    private $meta = 0;
    
    function __construct($meta = 0) {
        $this->meta = $meta;
    }
    
    // +检测值，返回boolean值 ,非法返回false
    private function checkValue($value) {
        if(preg_match("/(.*)(script|javascript|alert|window|document\.write)(.*)/im",$value)) {
            return false;
        }else {
            return true;
        }
    }

    // +转义值，返回转义后的值
    private function escape($value) {
        $value = htmlspecialchars($value);
        $value = addslashes($value);
        return $value;
    }
    
    // +切换 检测/屏蔽
    function changeMeta() {
        $this->meta = $this->meta==1 ? 0 : 1;
    }
    
    // +检测入口，传0检测，传1屏蔽
    function check($value) {
        if(0==$this->meta) {
            return $this->checkValue($value);
        }else if(1==$this->meta) {
            return $this->escape($value);
        }
    }
}
?>