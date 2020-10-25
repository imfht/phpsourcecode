<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 招募
 */
namespace app\green\controller\api\v1;
use app\common\controller\Api;
use app\green\model\GreenJob;
use app\green\model\GreenNews;
use app\green\model\GreenRecruit;


class Job extends Api {
    /**
     * 招募列表
     */
    public function index(){
        $param['sign'] = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(500,'签名验证失败');
        }
        $info   = GreenRecruit::where(['member_miniapp_id' => $this->miniapp_id])->order('sort desc')->select();
        if(empty($info)){
            return enjson(204,'empty');
        }else{
            return enjson(200,'success', $info);
        }
    }

    /**
     * 招募详情
     */
    public function detail(){
        $param['news_id'] = $this->request->param('news_id');
        $param['sign']    = $this->request->param('sign');
        $rel              = $this->apiSign($param);
        if ($rel['code'] != 200) {
            return enjson(500, '签名验证失败');
        }
        $info = GreenNews::where(['member_miniapp_id' => $this->miniapp_id, 'id' => $param['news_id']])->find();
        if (empty($info)) {
            return enjson(204, 'empty');
        } else {
            return enjson(200, 'success', $info);
        }
    }

    /**
     * @return \think\response\Json
     * 申请加入
     */
    public function add(){
        $this->isUserAuth();
        $param['name']       = $this->request->param('name/s', '');
        $param['city']       = $this->request->param('city/s', '');
        $param['occupation'] = $this->request->param('occupation/s', '');
        $param['card']       = $this->request->param('card/s', '');
        $param['front']      = $this->request->param('front/s', '');
        $param['back']       = $this->request->param('back/s', '');
        $param['signkey']    = $this->request->param('signkey');
        $param['sign']       = $this->request->param('sign');
        $param['uid']        = $this->user->id;
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson($rel['code'],'签名验证失败');
        }
        $validate = $this->validate($param,'Job.edit');
        if(true !== $validate){
            return enjson(0,$validate);
        }
        $info = GreenJob::create(['member_miniapp_id' => $this->miniapp_id,'uid' => $param['uid'],'name' => $param['name'],
            'city' => $param['city'], 'occupation' => $param['occupation'], 'card' => $param['card'], 'front' => $param['front'], 'back' => $param['back'], 'create_time' => time()]);
        if($info){
            return enjson(200,'成功',$info);
        }else{
            return enjson(0,'失败');
        }

    }
}