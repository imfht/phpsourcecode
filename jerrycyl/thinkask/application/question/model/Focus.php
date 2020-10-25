<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */

namespace app\question\model;
use think\Db;
use think\Model;

class Focus extends Model
{
    protected $request;

	//临时注释
	//关注问题
    public function focus($data){
		$data['add_time']=time();
       return  Db::name('question_focus')-> insertGetId($data);
    }
	//临时注释
	//取消关注问题(当前直接删除对应数据)
	public function unfocus($data){
		return Db::name('question_focus')->where('question_id',$data['question_id'])->where('uid',$data['uid'])->delete();
	}
	//临时注释
	//获取问题的关注状态
	public function get_focus_st($data){
		return Db::name('question_focus')->where('question_id',$data['question_id'])->where('uid',$data['uid'])->select();
	}
}