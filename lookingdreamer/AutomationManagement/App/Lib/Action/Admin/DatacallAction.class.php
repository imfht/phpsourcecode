<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class DatacallAction extends CommonAction {
	
    public function _before_add() {
		$list = explode(",", C("FOCUSPIC_PATTERN"));
		foreach($list as $val){
			if($val){
				$temp = explode(":", $val);
				$pattern[] = array("value" => trim($temp[0]), "text" => trim($temp[1]));
			}
		}
		modulelist($callmodules);
		$this->assign("pattern",$pattern);
		$this->assign("callmodules",$callmodules);
		$this->assign("tplfiles",$this->get_tplfiles());
    }		
    
    public function _before_edit() {
		$list = explode(",", C("FOCUSPIC_PATTERN"));
		foreach($list as $val){
			if($val){
				$temp = explode(":", $val);
				$pattern[] = array("value" => trim($temp[0]), "text" => trim($temp[1]));
			}
		}
		modulelist($callmodules);
		$this->assign("pattern",$pattern);
		$this->assign("callmodules",$callmodules);
		$this->assign("tplfiles",$this->get_tplfiles());
    }	

	// 缓存文件
	public function cache($name='',$fields='') {
		//$name	=	$name?	$name	:	$this->getActionName();
		$iscache = false;
		$Model	=	M("Datacall");
		$list		=	$Model->order('id desc')->select();
		$data		=	array();
		$config = array();
		foreach ($list as $key=>$val){
    		$data['datacall_'.$val['calltype']][$val["callcode"]] =	$val;
    		$config['datacall_'.$val["callcode"]] = $val['status'] ? true : false;
		}
		foreach($data as $key => $val){
			$savefile =	$this->getCacheFilename($key);
			// 所有参数统一为大写
			$content = "<?php\nreturn ".var_export(array_change_key_case($val,CASE_UPPER),true).";\n?>";
			$iscache = file_put_contents($savefile,$content);
		}
		$savefile =	$this->getCacheFilename('_datacall_config');
		$content = "<?php\nreturn ".var_export(array_change_key_case($config,CASE_UPPER),true).";\n?>";
		$iscache = file_put_contents($savefile,$content);

		if($iscache){
			$this->success('缓存生成成功！');
		}else{
			$this->error('缓存失败！');
		}
	}
	
	private function get_tplfiles(){
		$dir = './App/Tpl/Home/Default/Datacall/';
		if (is_dir($dir) && $handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if($file != '.' && $file !== '..') {
					$cur_path = $dir . '/' . $file;
					if(!is_dir($cur_path)) {
						$val = array(
							'name' => $file,
							'title' => $file,
						);
						$list[] = $val;
					}
				}
			}
		}
		return $list;	
	}
}
?>