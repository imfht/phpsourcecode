<?php
namespace Common\Model;
use Think\Model;

class AttachmentHandleModel extends Model{

//同步添加/删除附属附件
// Array ( [thumb] => [add_time] => 2015-07-10 10:56:59 [cid] => 25 [title] => sdasd [keywords] => asd [description] => asdasd [content] => asdasdasd [id] => 561 )
// Array ( [table] => sy_article [model] => Article )
	public function _after_insert($data,$options){
		D('Common/Attachment')->addFile($data,$data['id'],$this->tableName());
	}

//同步添加/删除附属附件
//Array ( [table] => sy_article [model] => Article [where] => Array ( [id] => 558 ) )
	public function _after_update($data,$options){
		D('Common/Attachment')->editFile($data,$data['id'],$this->tableName());
	}

//同步删除附属附件
//Array( [id] => 555)
//Array( [where] => Array ( [id] => 554 ) [table] => sy_article [model] => Article)
	public function _after_delete($data,$options) {
		D('Common/Attachment')->delFile($data['id'],$this->tableName());
	}

//自动获取不带前缀的表名
	public function tableName($prefix=false){
		$true_table_name=$this->getTableName();
		if($prefix) return $true_table_name;
		else return str_replace($this->tablePrefix, '', $true_table_name);
	}

}
?>