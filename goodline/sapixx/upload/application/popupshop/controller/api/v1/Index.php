<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\popupshop\model\Article;
use app\popupshop\model\Config;
use app\popupshop\model\Store;
use think\facade\Request;

class Index extends Base{

    /**
     * 获取配置
     */
    public function config(){
        $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
        if($this->user){
            $config['is_reg_store'] = Store::where(['uid' => $this->user->id ])->count();
        }else{
            $config['is_reg_store'] = 0;
        }
        $config['set_tab_style'] = 1;
        return json(['code'=>200,'msg'=>'成功','data' => $config]);
    }

    /**
     * 获得首页公告
     */
    public function notice(){
        $data['signkey'] = Request::param('signkey');
        $data['sign']    = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] == 200){
            $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
            $condition[] = ['types','=',2];
            $result = Article::where($condition)->field('id,title')->order('id desc')->find();
            if(!empty($result)){
                return enjson(200,'成功',$result->toArray());
            }
        }
        return enjson(204,'未输入关键字');
    }
}