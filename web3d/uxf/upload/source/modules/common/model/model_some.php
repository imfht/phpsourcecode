<?php
/**
 * 单表Db模型类
 */
class Model_Some extends Mvc_Model {
    protected $_table = 'tablename';
    protected $_pk = 'id';
    
    public function foo(){
        return 'Hello, Uxf!';
    }
}
