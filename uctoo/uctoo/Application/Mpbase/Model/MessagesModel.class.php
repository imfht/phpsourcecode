<?php
/**
 * Created by PhpStorm.
 * User: Uctoo-Near
 * Date: 2016/4/12
 * Time: 15:15
 */
namespace Mpbase\Model;
use Think\Model;

class MessagesModel extends Model
{


    public function get_replay_all($where = null, $r = 10, $page = 1)
    {
        $model = D('replay_messages');
        $select['data'] = $model->where($where)->page($page, $r)->select();

        foreach ($select['data'] as &$v) {
            switch ($v['type']) {
                case'text':
                    $v['editaction'] = '<a href="' . U('edit_text_messages', array('id' => $v['id'])) . '">查看or编辑</a>';
                    break;
                case'picture':
                    $v['editaction'] = '<a href="' . U('edit_picture_messages', array('id' => $v['id'])) . '">查看or编辑</a>';
                    break;
            }

            switch ($v['mtype']) {
                case 1 :
                    $v['mtype'] = '关注自动回复';
                    !$v['statu'] ? $statu = array('url' => 'Admin/Mpbase/open_mes_uq', 'text' => '启用') : $statu = array('url' => 'Admin/Mpbase/close_mes', 'text' => '停用');
                    $v['statu'] = '<a href="' . U($statu['url'], array('id' => $v['id'])) . '">' . $statu['text'] . '</a>';
                    break;
                case 2 :
                    $v['mtype'] = '消息自动回复';
                    !$v['statu'] ? $statu = array('url' => 'Admin/Mpbase/open_mes_uq', 'text' => '启用') : $statu = array('url' => 'Admin/Mpbase/close_mes', 'text' => '停用');
                    $v['statu'] = '<a href="' . U($statu['url'], array('id' => $v['id'])) . '">' . $statu['text'] . '</a>';

                    break;
                case 3 :
                    $v['mtype'] = '关键词自动回复';
                    !$v['statu'] ? $statu = array('url' => 'Admin/Mpbase/open_mes_kw', 'text' => '启用') : $statu = array('url' => 'Admin/Mpbase/close_mes', 'text' => '停用');
                    $v['statu'] = '<a href="' . U($statu['url'], array('id' => $v['id'])) . '">' . $statu['text'] . '</a>';

                    break;
            }

        }


        $select['count'] = $model->where($where)->count();

        return $select;

    }


    public function get_replay_byid($id)
    {

        $model = D('replay_messages');
        $select = $model->find($id);

        return $select;


    }


    public function  set_unqiue($id)
    {

        $model = D('replay_messages');

        $model->startTrans();
        $mtype = $model ->find($id);
        $where['mtype'] = $mtype['mtype'];
        $where['statu'] = 1;
        $where['mp_id']=get_mpid();
        $data['statu'] = 0;
        $select = $model->where($where)->select();

        foreach ($select as $v) {
            $sid = $v['id'];
            $res_set = $model->where("id=$sid")->save($data);
            if (!$res_set) {
                $model->rollback();
                return -1;
            }
        }

        unset($where);
        unset($data);
        $where['id'] = $id;
        $where['mtype'] = $mtype['mtype'];
        $data['statu'] = 1;
        $res = $model->where($where)->field('statu')->save($data);
        //dump($model->getlastsql());die;
        // dump($res);
        if ($res) {
            $model->commit();
            return true;
        } else {
            $model->rollback();
            return false;
        }


    }

    public function  set_open($id)
    {

        $model = D('replay_messages');
        $where['id'] = $id;
        $where['mp_id']=get_mpid();
        $data['statu'] = 1;
        $res = $model->where($where)->field('statu')->save($data);
        //dump($model->getlastsql());die;
        // dump($res);
        if ($res) {
            return true;
        } else {
            return false;
        }


    }

    public function  close_open($id){

        $model = D('replay_messages');
        $where['id'] = $id;
        $data['statu'] = 0;
        $res = $model->where($where)->field('statu')->save($data);
        //dump($model->getlastsql());die;
        // dump($res);
        if ($res) {
            return true;
        } else {
            return false;
        }


    }


/*
 * 没接收到微信信息;
 *
 * */

    public function replay_mes_attention($params){
//        $data = $params['weObj']->getRevData();
        $model = D('replay_messages');
        $where['mtype']=1;
        $where['statu']=1;
        $data = $model->where($where)->find();
        $ms_id['ms_id']=$data['ms_id'];
        switch($data['type']){
            case 'text':
                $rep_data = M('text_messages')->where($ms_id)->find();
                $params['weObj']->text($rep_data['detile']);
        }


        $test['text'] = '1111111';
        M('test')->add($test);

    }
//
//    public function aaa(){
//        $test['test']
//        M('test')->add($test);
//
//    }


}
