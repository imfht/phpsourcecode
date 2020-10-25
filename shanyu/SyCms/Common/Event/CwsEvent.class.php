<?php
namespace Common\Event;

//中文分词
class CwsEvent{
	public $error='';

	//获取中文分词数组
	//ArticleModel->getKeywords()
	public function getWords($string='',$length=5){
		if(empty($string)) return array();
		//检测词库是否存在
		if(is_file(COMMON_PATH.'Lib/Scws/etc/dict.utf8.xdb')){

			lib("Scws.pscws4");
			$Pscws= new \PSCWS4();
			$Pscws->set_dict(COMMON_PATH.'Lib/Scws/etc/dict.utf8.xdb');
			$Pscws->set_rule(COMMON_PATH.'Lib/Scws/etc/rules.utf8.ini');
			$Pscws->set_ignore(true);//忽略特殊字符
			$Pscws->send_text($string);
			$words=$Pscws->get_tops($length);
			$Pscws->close();
			
			$result=array();
			foreach ($words as $v) {
				$result[] = $v['word'];
			}
			return $result;
		}else{
			$this->error='dict.utf8.xdb词库不存在';
			return false;
		}
		
	}
}