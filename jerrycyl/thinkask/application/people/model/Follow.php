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

namespace app\people\model;
use think\Db;
use think\Model;

class Follow extends Model
{
    protected $request;

	//获取粉丝和关注数量
    public function fans_count($uid){
       return  Db::name('user_follow')-> where('friend_uid',$uid)->count();
    }
    public function friend_count($fr_uid)
    {
        return Db::name('user_follow')->where('fans_uid',$fr_uid)->count();
    }
}