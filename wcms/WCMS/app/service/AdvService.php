<?php
class AdvService {
	
	private $_status = array (0 => "显示", - 1 => "隐藏" );
	private $_type = array (5=>"组别1",0 => "组别2",2 => "组别3" , 1 => "组别4", 4=>"组别5",3=>"组别6", 6=>"组别7");
	
	public function getAdvByStatus($status) {
		return AdvModel::instance ()->getAdvByStatus ( $status );
	}
	
	public function getType() {
		return $this->_type;
	}
	
	public function addAdv($params) {
		$image = $this->upload ();
		$params ['image'] = $image ['message'];
		$params ['add_time'] = time ();
		$flag = AdvModel::instance ()->addAdv ( $params );
		if ($flag < 1) {
			echo "添加失败";
		} else {
			echo "添加成功";
		}
	}

	public function removeAdvById($id) {
		$flag = AdvModel::instance ()->removeAdvById ( $id );
		if ($flag < 1) {
			echo "删除失败";
		} else {
			echo "删除成功";
		}
	}
	
	public function saveAdvStatusById($id) {
		$adv = $this->getAdvById ( $id );
		$status = $adv ['status'] == 0 ? - 1 : 0;
		$rs = AdvModel::instance ()->saveAdvById ( array ('status' => $status ), $id );
		if ($rs < 1) {
			return array ('status' => false, 'message' => "更新失败" );
		} else {
			return array ('status' => true, 'message' => "更新成功", 'data' => $this->_status [$status] );
		}
	}
	
	public function saveAdvById($v, $id) {
		unset ( $v ['id'] );
		
		$image = $this->upload ();
		if (! empty ( $image ['message'] )) {
			$v ['image'] = $image ['message'];
		}
		$rs = AdvModel::instance ()->saveAdvById ( $v, $id );
		if ($rs < 1) {
			return array ('status' => false, 'message' => "更新失败" );
		} else {
			return array ('status' => true, 'message' => "更新成功" );
		}
	}
	
	public function getAdvByType($type) {
		return AdvModel::instance ()->getAdvByType ($type);
	}
	
	public function getAdvById($id) {
		return AdvModel::instance ()->getAdvById ( $id );
	}
	
	public function getAllAdv() {
		$rs = AdvModel::instance ()->getAllAdv ();
		foreach ( $rs as $k => $v ) {
			$rs [$k] ['status'] = $this->_status [$v ['status']];
			$rs [$k] ['type'] = $this->_type [$v ['type']];
		}
		return $rs;
	}
	
	/**
	 * 上传头像
	 */
	private function upload() {
		$image = new Image ();
		$image->thumb_maxwidth = 2000;
		$image->thumb_maxheight = 2000;
		return $image->upload ( $_FILES ['image'], "adv", false );
	}

}

class AdvModel extends Db {
	
	private $_adv = 'd_buy_adv';
	
	public function addAdv($params) {
		return $this->add ( $this->_adv, $params );
	}
	
	public function saveAdvById($v, $id) {
		return $this->update ( $this->_adv, $v, array ('id' => $id ) );
	}
	
	public function removeAdvById($id) {
		return $this->delete ( $this->_adv, array ('id' => $id ) );
	}
	
	public function getAdvByType($type) {
		return $this->getAll ( $this->_adv, array ('type' => $type, 'status' => 0 ),null,'id DESC' );
	}
	
	public function getAdvById($id) {
		return $this->getOne ( $this->_adv, array ('id' => $id ) );
	}
	
	public function getAllAdv() {
		return $this->getAll ( $this->_adv, null, null, 'id desc' );
	}
	
	public function getAdvByStatus($status) {
		return $this->getAll ( $this->_adv, array ('status' => $status ) );
	}
	
	/**
	 * 
	 * @return AdvModel
	 */
	public static function instance() {
		return parent::_instance ( __CLASS__ );
	}
}