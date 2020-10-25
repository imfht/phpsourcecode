<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/3
 * Time: 13:48
 */
namespace app\first\controller;

use app\first\model\Carousel as CarouselModel;
use app\first\validate\Carousel as CarouselValidate;

/**
 * Class Carousel
 * @title 轮播图相关
 * @url /v1/carousel
 * @desc  有关于轮播图
 * @version 1.0
 * @package app\first\controller
 */
class Carousel extends Base{
    //是否开启授权认证
    public $apiAuth = true;
    //附加方法
    protected $extraActionList = [];

    public function __construct(){
        parent::__construct();
    }

    /**
     * @title 获取轮播图
     * @method get
     * @param int $limit true 条数 默认:3
     * @param string $order true 条数 默认:desc asc|desc
     */
    public function get($limit=3,$order='desc'){
        $carousel = new CarouselModel;
        $list = $carousel::limit($limit)
            ->field('id,title,url,keywords,description,image')
            ->order("id {$order}")
            ->select();
        if(!$list){
            return $this->sendError(0,lang('error',lang('query')));
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('saved')])]),
            'data'=>$list
        ]);
    }

    /**
     * @title 保存轮播
     * @method save
     * @param string $title 名称 true
     * @param string $image 图片文件 true
     * @return json data 返回结果
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function save(){
        $data = request()->param();
        $validate = new CarouselValidate;
        if(!$validate->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $carousel = new CarouselModel;
        $upload = new Uploadify();
        $head = $upload->upload_head('image');
        if(!$head['status']){
            return $this->sendError(0,$head['message']);
        }
        $data['image']=$head['path'];
        if(!$carousel->allowField(true)->save($data)){
            return $this->sendError(0,$carousel->getError());
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('done',[lang('success',[lang('saved')])])
        ]);
    }

    /**
     * @title 删除
     * @method delete
     * @param int $id 轮播图id true
     * @return json data 返回结果
     */
    public function delete($id=''){
        $validate = new CarouselValidate;
        if(!$validate->scene('delete')->check(['id'=>$id])){
            return $this->sendError(0,$validate->getError());
        }
        $carousel = new CarouselModel;
        if(!$carousel::get($id)){
            return $this->sendError(0,lang('unalready',[lang('carousel')]));
        }
        if(!$carousel::destroy($id)){
            return $this->sendError(0,lang('error',[lang('delete')]));
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('success',[lang('delete')])
        ]);
    }
}