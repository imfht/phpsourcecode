<?php
namespace app\common\behavior;

use think\Db;

/**
 * 安全防范处理
 * @package app\common\behavior
 */
class Safe{
    protected $words = [];
    protected $funs = [];
	public function run(&$params){
	    $webdb = cache('webdb');
	    $config = $webdb['P__safe365'];
	    if (empty($config['safe365_is_open']) || $_POST['safe365_pwd']==$webdb['mymd5']) {
	        return ;
	    }
	    $this->words = str_array($config['danger_word']);
	    $this->funs = str_array($config['forbid_php_fun']);
	    $get_post = input();
	    $data = $config['fun_avoid_level']==2 ? $get_post : $_GET;
	    foreach ($data AS $key=>$value){
	        if ($this->check_fun($value)===true){
	            $this->add($data);
	            die('包含有危险的函数');
	        }
	    }
	    
	    foreach ($get_post AS $key=>$value){
	        if ($this->check_keyword($value)===true) {
	            $this->add($data);
	            die('包含有危险的字符');
	        }
	    }	    
	}
	
	protected function add($data=[]){
	    $arr = [];
	    foreach($data AS $key=>$value){
	        $arr[] = "$key###\t###$value";
	    }
	    $url = request()->url(true);
	    $array = [
	            'ip'=>get_ip(),
	            'create_time'=>time(),
	            'url'=>$url,
	            'par_post'=>implode("\r\n@#@#@#@\r\n", $arr),
	    ];
	    if (!get_cookie(md5($url))) {
	        set_cookie(md5($url),1);
	        Db::name('safe365_logs')->insert($array);
	    }	    
	}
	
	
	protected function check_keyword($value){
	    foreach($this->words AS $w){
	        if (strstr($value,$w)) {
	            return true;
	        }
	    }
	}
	
	protected function check_fun($value){
	    foreach($this->funs AS $fun){
	        if (preg_match("/$fun([ \r\t\n]*)\(/i", $value)) {
	            return true;
	        }
	    }
	}
	
	
}