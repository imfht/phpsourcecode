<?php
namespace App\Model;
use Swoole;
class Stats extends Swoole\Model {

	public $table = 'stats_';
	
	public $primary = 'id';
		
	public function attributeLabels(){
		return [
			'id'=>'ID','interface_id'=>'','module_id'=>'','time_key'=>'','date_key'=>'','total_count'=>'','fail_count'=>'','total_time'=>'','total_fail_time'=>'','avg_time'=>'','avg_fail_time'=>'','max_time'=>'最长时间','min_time'=>'最小时间','fail_server'=>'','succ_server'=>'','total_server'=>'','fail_client'=>'','succ_client'=>'','total_client'=>'','ret_code'=>'','succ_ret_code'=>'',
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
