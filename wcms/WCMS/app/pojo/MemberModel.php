<?php
/**
 * 
 * Enter description here ...
 * @author Administrator
 *
 */
class MemberModel extends Db {
	
	protected $_member_list = 'w_member_list';
	protected $_member_group = 'w_member_group';


	public function addMember($arr) {
		$this->add ( $this->_member_list, $arr );
		return $this->lastInsertId ();
	
	}

	public function getMemberByUid($uid){
	    return $this->getOne($this->_member_list,array('uid'=>$uid));
    }

	public function getAllMemeber(){
	    return $this->getAll($this->_member_list,null,null,'uid desc');
    }

    public function removeMemberByUid($uid){
	    return $this->delete($this->_member_list,array('uid'=>$uid));
    }

	public function getMemberByMobile($mobile){
	    return $this->getOne($this->_member_list,array('mobile_phone'=>$mobile));
    }

    public function saveMemberByUid($v,$uid){
	    return $this->update($this->_member_list,$v,array('uid'=>$uid));
    }

	
	/**
	 * 返回MemberModel
	 *
	 * @return MemberModel
	 */
	public static function instance() {
		return parent::_instance ( __CLASS__ );
	}
}