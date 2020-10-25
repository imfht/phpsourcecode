<?php
namespace App\Model;
use Swoole;
class Category extends Swoole\Model {

	public $table = 'category';
	
	public $primary = 'cate_id';
		
	public function attributeLabels(){
		return [
			'cate_id'=>'CATE_ID','cate_name'=>'分类名称','ctime'=>'创建时间',
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
