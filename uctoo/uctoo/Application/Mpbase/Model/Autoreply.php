<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\mpbase\model;
use app\admin\builder\AdminConfigBuilder;
use Think\Model;

/**
 * Class Autoreply 自动回复模型
 * @package Mpbase\Model
 * @auth patrick
 */
class Autoreply extends Model {

    public function getArType($key = null,$type = 0){
        //解决关注回复不能是多图文
        if($type){
            $array = array( 2 => '消息自动回复', 3 => '关键词自动回复');
        }else{
            $array = array(1 => '关注自动回复', 2 => '消息自动回复', 3 => '关键词自动回复');
        }
        return !isset($key)?$array:$array[$key];
    }

    public function getMessagesType(){
        $array =array(
            array('id'=>0,'value' =>'全部'),
            array('id'=>1,'value' =>'关注自动回复'),
            array('id'=>2,'value' =>'消息自动回复'),
            array('id'=>3,'value' =>'关键词自动回复')
        );
        return $array;
    }

    public function replyMessagesType(){
        $array =array(
            array('id'=>0,'value' =>'全部'),
            array('id'=>1,'value' =>'文本'),
            array('id'=>2,'value' =>'图文'),
            array('id'=>3,'value' =>'待定')
        );
        return $array;
    }
    public function addAr($data)
    {
        $res = $this->save($data);
        return $res;
    }

    public function getAr($where){
        $mp = $this->where($where)->find();
        return $mp;
    }

    public function getList($where){
        $list = $this->where($where)->select();
        return $list;
    }

    public function editAr($data)
    {
        $res = $this->save($data);
        return $res;
    }

/*
 * 然并卵的东西
 * */
    public function builder_picture_messages(){
        $builder = new AdminConfigBuilder();
        $builder

            ->keyText('title0','标题','第一条')
            ->keyText('detile0','内容')
            ->keyText('url0','url0')
            ->keyText('title1','标题','第二条')
            ->keyText('detile1','内容')
            ->keyText('url1','url1')
            ->keyText('title2','标题','第三条')
            ->keyText('detile2','内容')
            ->keyText('url2','url2')
            ->keyText('title3','标题','第四条')
            ->keyText('detile3','内容')
            ->keyText('url3','url3')
            ->keyText('title4','标题','第五条')
            ->keyText('detile4','内容')
            ->keyText('url4','url4')
            ->keyMultiImage('pic','图片','按顺序添加图片');


    }

    /*
     *判断类型选择数据库
     *
     * */
    public function post_messages($data){

        switch($data['type']){
            case 'picture':
                $picturemodel = db('picture_messages');
                !$data['ms_id']?
                $res = $picturemodel->field('title0,detile0,url0,title1,detile1,url1,title2,detile2,url2,title3,detile3,url3,title4,detile4,url4,pic')->add($data):
                $res = $picturemodel->field('title0,detile0,url0,title1,detile1,url1,title2,detile2,url2,title3,detile3,url3,title4,detile4,url4,pic')->save($data);
                break;
            case 'text':
                $textmodel = db('text_messages');
                !$data['ms_id']?
                $res = $textmodel->field('detile')->update($data):
                $res = $textmodel->field('id,detile')->update($data);
                break;
            default:
                return false;
        }

        return $res;
    }


    public function get_mes_data($data){

        $res_data= $this->get_type_data($data);
        return array_merge($res_data,$data);


    }

    public function get_type_data($data){

        $id = $data['ms_id'];

        switch($data['type']){
            case 'picture':
                $model = db('picture_messages');
                break;
            case 'text':
                $model = db('text_messages');
                break;
        }

        return $model->find($id);
    }


}