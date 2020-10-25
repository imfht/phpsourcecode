<?php
namespace App\Model;
use Swoole;
class Interfaces extends Swoole\Model {

	public $table = 'interface';
	
	public $primary = 'id';

	public function attributeLabels(){
		return [
			'id'=>'ID','name'=>'接口名称','own_uid'=>'接口所属用户','create_time'=>'',
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
