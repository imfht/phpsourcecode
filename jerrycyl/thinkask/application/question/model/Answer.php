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

class Answer extends Model
{
    protected $request;

    private function add($comm){
        $comm['add_time']=time();
       return  Db::name('Answer')-> insertGetId($comm);
    }
    private function edit($comm){
        return  Db::name('Answer')->where('question_id',$comm['question_id'])->where('uid',$comm['uid'])-> update($comm);
    }
    /**保存评论
     * @param $data
     * @return mixed
     */
    public function save_answer_content($comm)
    {
        if (empty($comm)){
            return null;
        }
        $comm['time'] = time();
		return $this->add($comm);
    }
	 /**编辑评论
     * @param $data
     * @return mixed
     */
    public function edit_answer_content($comm)
    {
        if (empty($comm)){
            return null;
        }
        $comm['time'] = time();
		return $this->edit($comm);
    }
    /**
     * 获取回复内容
     */
    public function get_answer_content($uid,$qid){
        return Db::name('question_comments')->where('question_id','=',$qid)->where('uid','=',$uid)->select();
    }
}