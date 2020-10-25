<?php
namespace App\Model;
use Swoole;
class Admin extends Swoole\Model {

	public $table = 'admin';
	
	public $primary = 'id';
		
	public function attributeLabels(){
		return [
			'id'=>'ID','username'=>'用户名','password'=>'密码','role'=>'角色','login_time'=>'登录时间','ctime'=>'创建时间',
		];
	}

	public function search($param) {
		return $this->gets($param);
	}

	public function create($data) {
		return $this->put($data);
	}

	public function update($id, $data) {
		return $this->set($id, $data);
	}

	public function delete($param) {
		return $this->dels($param);
	}
	public function getData(){
		$data=[];
		$att=  $this->attributeLabels();
		foreach ($att as $key=>$value){
			if(getRequest($key)){
				$data[$key]=  getRequest($key);
			}
		}
		return $data;
	}

}	
