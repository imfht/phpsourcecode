<?php
/**
 * 用户信息管理
 */
class userinfoAction extends backendAction
{

    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('userinfo');
    }
     public function _before_edit() {

     	$this->assign('areadata', true);
     
     } 
    

   public function _before_update($data) {
        
        $birthday = $this->_post('birthday', 'trim');
        if ($birthday) {
            $birthday = explode('-', $birthday);
            $data['byear'] = $birthday[0];
            $data['bmonth'] = $birthday[1];
            $data['bday'] = $birthday[2];
        }
        return $data;
    }

   

}