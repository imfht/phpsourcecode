<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;

class Jump extends IndexBase
{
    /**
     * 进入用户的圈子
     * @param number $uid
     */
    public function qun($uid=0)
    {
        if (empty($uid)) {
            $uid = $this->user['uid'];
        }
        if (empty($uid)) {
            $this->error('UID不存在');
        }
        if (modules_config('qun')) {
            $id = Db::name('qun_content1')->where('uid',$uid)->order('usernum desc')->value('id');
            if (empty($id)) {
                $id = Db::name('qun_content')->where('uid',$uid)->value('id');
            }            
            if (empty($id)) {
                $url = get_url('user',$uid);
            }else{
                $url = urls('qun/content/show',['id'=>$id]);
            }
        }else{
            $url = get_url('user',$uid);
        }
        $this->redirect($url,[],301);
    }
}