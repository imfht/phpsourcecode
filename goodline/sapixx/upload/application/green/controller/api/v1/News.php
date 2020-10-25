<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\green\controller\api\v1;
use app\common\controller\Api;
use app\green\model\GreenNews;
use app\green\model\GreenNewsCate;
use util\Util;

class News extends Api{
    
    /**
     * 获取商城的配置
     * @return void
     */
    public function index(){
        $result = GreenNews::where(['member_miniapp_id' => $this->miniapp_id])->field('id,title,types,cate_name,cate_id,desc,views,img')->limit(8)->select();
        if(empty($result)){
            return enjson(403,'没有内容');
        }else{
            $data = [];
            foreach ($result as $key => $value) {
               $data[$key] = $value;
               $data[$key]['img'] = $value['img'].'?x-oss-process=style/300';
               $data[$key]['state_text'] = $value['types'] == 2 ? '热点' : '';
            }
            return enjson(200,'成功',$data);
        }
    }

    /**
     * 获取商城的配置
     * @return void
     */
    public function notice(){
        $result = GreenNews::where(['member_miniapp_id' => $this->miniapp_id,'types'=>4])->field('id,title')->find();
        if(empty($result)){
            return enjson(204,'空');
        }else{
            return enjson(200,'成功',$result);
        }
    }

    /**
     * 新闻分类
     * @return void
     */
    public function cate(){
        $data = GreenNewsCate::where(['member_miniapp_id' => $this->miniapp_id])->field('id,picture,name')->order('sort desc,id desc')->select()->toArray();
        return enjson(200,'成功',$data);
    }

    /**
     * 新闻列表
     * @return void
     */
    public function lists(int $cate_id){
        $result = GreenNews::where(['member_miniapp_id' => $this->miniapp_id,'cate_id' => $cate_id])->field('id,title,types,cate_name,cate_id,desc,views,img,update_time')->paginate(20,true)->toArray();
        if(empty($result)){
            return enjson(204,'空');
        }else{
            $data = [];
            foreach ($result['data'] as $key => $value) {
                $data[$key] =  $value;
                $data[$key]['update_time'] =  Util::ftime($value['update_time']);
            }
            return enjson(200,'成功',$data);
        }
    }
}