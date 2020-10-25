<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 网页服务
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Article;
use filter\Filter;
use GuzzleHttp\Client;

class Webview extends Base{

    /**
     * 内容管理
     */
    public function index(int $id){
        $view['info']  = Article::where(['member_miniapp_id' =>$this->miniapp_id,'id' => $id])->order('id desc')->find();
        $view['title'] = $view['info']['title'];
        return view('v3/webview/index')->assign($view);
    }

    /**
     * 服务协议
     */
    public function contract(){
        $view['info'] = Article::where(['member_miniapp_id' =>$this->miniapp_id,'types' => 1])->order('id desc')->find();
        $view['title'] = '服务协议';
        return view('v3/webview/contract')->assign($view);
    }

    /**
     * 我的特权
     */
    public function service(){
        $view['info']  = Article::where(['member_miniapp_id' =>$this->miniapp_id,'types' => 2])->order('id desc')->find();
        $view['title'] = '我的特权';
        return view('v3/webview/service')->assign($view);
    }

    /**
     * 查询物流信息
     */
    public function express($ids){
        $condition['order_no']       = Filter::filter_escape($ids);
        $condition['is_del']         = 0;
        $condition['paid_at']        = 1;
        $condition['express_status'] = 1;
        $rel = model('Order')->where($condition)->field('express_no')->find();
        if(empty($rel)){
            $rel = model('Shopping')->where($condition)->field('express_no')->find();
            if(empty($rel)){
                $view['express'] = json_encode(['code'=>201,'msg'=>'未找到当前订单']);
            }
        }
        $client = new Client([
            'base_uri' => 'https://goexpress.market.alicloudapi.com/',
            'timeout'  => 2.0,
        ]);
        $response = $client->request('GET','/goexpress?no='.$rel['express_no'],[
            'headers' => [
                'Authorization' => 'APPCODE b80984cdd0f5479cbec9ce104c9addbe',
                'Accept'     => 'application/json',
            ]
        ]);
        $view['express'] = $response->getBody();
        $view['title'] = '物流查询';
        return view('v3/webview/express')->assign($view);
    } 
}