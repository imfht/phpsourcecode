<?php
namespace Home\Model;

use Think\Model;

class TopicModel extends Model
{
    public function info($id){
        $where['id'] = $id;
        return $this->where($where)->find();
    }
    public function hotTopic($limit = 10){
        $where = array(
            'status' => 1
        );
        $res =  $this->where($where)->order('hits DESC,id DESC')->limit($limit)->select();
        foreach ($res as $k => $v){
            $res[$k]['pic_url'] = get_image_url($res[$k]['pic_url']);
        }
        return $res;
    }
}

?>