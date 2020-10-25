<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 消息日志基础类
*/

defined('INPOP') or exit('Access Denied');

class mnslog extends Model{

	//初始化
    public function __construct(){
		parent::__construct("mnslogs", "mnslogid");
    }
	
}

?>