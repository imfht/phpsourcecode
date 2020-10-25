<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Article;
use app\fastshop\model\Config;
use app\fastshop\model\Store;
use app\fastshop\model\RegNum;
use app\fastshop\model\Vip;
use think\facade\Request;

class Index extends Base{

    /**
     * 获取配置
     */
    public function config(){
        if($this->user){
            RegNum::countMum($this->miniapp_id,$this->user->id);   //统计直推人数
            $vip      = Vip::where(['member_miniapp_id' => $this->miniapp_id,'user_id' => $this->user->id,'state' => 1])->find(); 
            $is_store = Store::where(['uid'=> $this->user->id])->count();
        }
        $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->field('shopping_name')->find();
        $config['shopping_name'] = $config->shopping_name;
        $config['is_vip']        = empty($vip) ? 0 : 1;
        $config['is_store']      = empty($is_store) ? 0 : 1;
        return enjson(200,'成功',$config);
    }

    /**
     * 
     * 获得首页公告
     */
    public function notice(){
        $data['signkey'] = Request::param('signkey');
        $data['sign']    = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] == 200){
            $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
            $condition[] = ['types','=',0];
            $result = Article::where($condition)->field('id,title')->order('id desc')->find();
            if(!empty($result)){
                return enjson(200,'成功',$result->toArray());
            }
        }
        return enjson(204,'签名失败');
    }

    /**
     * 读取是否开启余额支付
     */
    public function shopBuyTypes(){
        $param['signkey'] = Request::param('signkey');
        $param['sign']    = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $info = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
        $data[] = ['name'=>'微信支付','types'=>1,'disabled' => false];
        if($info->payment_type_shop){
            $data[] = ['name'=>'余额支付','types'=>2,'disabled' => false];
        }
        return enjson(200,'成功',$data);
    }


    /**
     * 抢购
     */
    public function saleBuyTypes(){
        $param['signkey'] = Request::param('signkey');
        $param['sign']    = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $info = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
        $data[] = ['name'=>'微信支付','types'=>1,'disabled' => false];
        if($info->payment_type){
            $data[] = ['name'=>'余额支付','types'=>2,'disabled' => false];
        }
        return enjson(200,'成功',$data);
    }
}