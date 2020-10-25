<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
class Index extends Controller{
    public function index(){ }
	public function showView(){
		/*$visitor = Db::table('visitor')->where('ip',$this->getUserSource())->find();
		if(!$visitor) Db::table('visitor')->insert(array('ip'=>$this->getUserSource(),'ua'=>$_SERVER['HTTP_USER_AGENT'],'last_ua'=>$_SERVER['HTTP_USER_AGENT'],'last_time'=>time()));else Db::table('visitor')->where('id',$visitor['id'])->update(['last_time'=>Db::raw(time()),'count'=>Db::raw('count+1'),'last_ua'=>$_SERVER['HTTP_USER_AGENT']]);*/
		return $this->fetch('index');
	}
	public function getUserSource(){
		$request = Request::instance();
		$ip = $request->ip();
		return $ip;
	}
}
?>