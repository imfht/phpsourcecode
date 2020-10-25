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

class Answer_comments extends Model
{
    protected $request;

    private function add($comm){
       return  Db::name('Answer_comments')-> insertGetId($comm);
    }

    /**保存回答评论
     * @param $data
     * @return mixed
     */
    public function save_comments_content($comm)
    {
        if (empty($comm)){
            return null;
        }
        $comm['time'] = time();
		return $this->add($comm);
    }

}