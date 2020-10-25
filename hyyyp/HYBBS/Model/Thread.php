<?php
namespace Model;
use HY\Model;
!defined('HY_PATH') && exit('HY_PATH not defined.');
class Thread extends Model {
    //通过文章ID 获取文章
    //文章ID
    public function read($id){
        //{hook m_thread_read_1}
        return $this->find("*",array(
            'id'=>$id
        ));
    }
    //通过文章ID获取文章标题
    //文章ID
    public function get_title($id){
        //{hook m_thread_get_title_1}
        return $this->find("title",array(
            'id'=>$id
        ));
    }
    //删除文章
    //主题ID
    public function del($id){
        //{hook m_thread_del_1}
        $this->delete(array(
            'id'=>$id
        ));
    }
    // 赞数操作 踩操作 评论数量操作
    // 主题ID
    // 数据结构KEY
    // 类型 + - 
    // 数值
    public function update_int($id,$key,$type = "+",$size = 1){
        //{hook m_thread_update_int_1}
        $key .= ($type=='+') ? '[+]' : '[-]';
        //{hook m_thread_update_int_2}
        $this->update(array(
            $key=>$size
        ),array(
            'id'=>$id
        ));
    }
    //获取主题列表
    //分页ID
    //获取个数
    //排序
    //分类ID - 指定ID
    //用户ID - 指定用户
    public function read_list($pageid , $size = 10,$order = "id DESC",$fid = -1,$uid = 0 ){
        //{hook m_thread_read_list_1}
        $data = array(

            "ORDER"=>$order,
            "LIMIT" => array(
                ($pageid-1) * $size,
                $size
            )
        );
        //{hook m_thread_read_list_2}
        if($fid != -1 && $uid != 0){
            $data['AND']=array(
                'fid'=>$fid,
                'uid'=>$uid,
            );
            //{hook m_thread_read_list_3}
        }elseif($fid != -1 && $uid == 0){
            $data['fid']=$fid;
            //{hook m_thread_read_list_4}
        }elseif($fid == -1 && $uid != 0){
            $data['uid']=$uid;
            //{hook m_thread_read_list_5}
        }

        //{hook m_thread_read_list_6}
        return $this->select('*',$data);
    }
    //----------搜索主题
    //
    //分页ID
    //获取个数
    //搜索关键字
    //排序方式
    //分类ID
    //用户ID
    // 
    public function search_list($pageid,$size,$key,$order = "id DESC",$fid = -1,$uid = 0){
        //{hook m_thread_search_list_1}
        $data = $this->select(
            "*",
            array(
                "title[~]" => $key,
                "ORDER"=>$order,
                "LIMIT" => array(
                    ($pageid-1) * $size, 
                    $size
                    )
                )
            );
        //{hook m_thread_search_list_2}
        return $data;
    }

    public function format(&$thread_list){
        //{hook m_thread_format_1}
        if(empty($thread_list))
            return;
        static $user_tmp = array();
        $User = M("User");
        //{hook m_thread_format_2}
        foreach ($thread_list as  &$v){
            //{hook m_thread_format_3}
            if(empty($user_tmp[$v['uid']])){
                $user_tmp[$v['uid']] = $User->id_to_user($v['uid']);
                
            }
            //{hook m_thread_format_4}

            if($v['buid']){
                if(empty($user_tmp[$v['buid']])){
                    $user_tmp[$v['buid']] = $User->id_to_user($v['buid']);
                    
                }

                $v['buser'] = $user_tmp[$v['buid']];
                $v['buser_avatar'] =$User->avatar($v['buser']);
            }
            //{hook m_thread_format_5}
            //UID获取用户名
            $v['user'] = $user_tmp[$v['uid']];
            //摘要去掉标签
            $v['summary'] = strip_tags($v['summary']);
            //$v['atime'] = humandate($v['atime']);
            $v['avatar']=$User->avatar($v['user']);
            if(!empty($v['img'])){
                $v['image']=explode(",", $v['img']);
                $v['image_count']=count($v['image'])-1;
            }
            //{hook m_thread_format_6}

        }

    }

    //判断用户是否回复过该主题
    public function is_user_post($uid,$tid){
        //{hook m_thread_is_user_post_1}
        return S("Post")->has(array("AND"=>array('uid'=>$uid,'tid'=>$tid)));
    }
    //{hook m_thread_fun}




}
