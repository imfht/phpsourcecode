<?php
/**
 * 文档控制器基类
 *
 * @author andery
 */
class docbaseAction extends frontendAction {
    public function _initialize() {
        parent::_initialize();
        //按照更新时间顺序
        
    }
    public function search() {
        return $map;
    }
}
