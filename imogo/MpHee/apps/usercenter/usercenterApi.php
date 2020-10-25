<?php
class usercenterApi extends baseApi{
	
	public function getMenu(){
		return array(
					'sort'=>1,
					'title'=>'用户中心',
					'list'=>array(
						'用户分组'=>url('usercenter/index/grouplist'),
						'用户列表'=>url('usercenter/index/userlist'),
					)
			);
	}
	
	public function addrecord($table='',$data){
		if( $data['addminus'] == 0 ){
			$tableinfo = $this->model->field('jifen,balance')->table('userinfo')->where( array('uuid'=>$data['uuid'],'ppid'=>$data['ppid']) )->find();
			if( $tableinfo[$table] < $data['number']){
				return "not enough";
			}
		}
		return $this->model->table('user'.$table.'cord')->data($data)->insert();
	}
	
	public function getfieldinfo($data){
		return $fieldinfo = $this->model->field($data['field'])->table('userinfo')->where( array('uuid'=>$data['uuid'],'ppid'=>$data['ppid']) )->find();
	}
	
	public function getrecord($table='',$condition){
		return $tablelist = $this->model->table('user'.$table.'cord')->where( $condition )->select();
	}
	
}