<?php
namespace Admin\Model;
use Think\Model;

class CommonModel extends Model {

	// 获取当前用户的ID
    public function getMemberId() {
        return isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
    }
    
    public function _after_find(&$result,$options) {
        
            
    }

    public function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }
}
?>