<?php
namespace Model;
use HY\Model;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class Post extends Model {

    // 通过 评论ID 获取评论数据
    public function read($id){
        //{hook m_post_read_1}
        return $this->find("*",array(
            'id'=>$id
        ));
    }
    //删除 某文章ID 的所有评论以及文章内容
    public function del_thread_all_post($id){
        //{hook m_post_del_thread_all_post_1}
        return $this->delete(array(
            'tid'=>$id
        ));
    }

    //通过 评论过ID 删除评论数据
    public function del($id){
        //{hook m_post_del_1}
        return $this->delete(array(
            'id'=>$id
        ));
    }
    //{hook m_post_fun}
}
