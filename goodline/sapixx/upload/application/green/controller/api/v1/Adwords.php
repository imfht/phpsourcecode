<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\green\controller\api\v1;
use app\green\controller\api\Base;
use app\green\model\GreenAdwords;

class Adwords extends Base{

    
    /**
     * 读取微信API
     * @param integer 读取ID
     * @return json
     */
    public function index(){
        $param['ids']  = $this->request->param('ids');
        $param['sign']  = $this->request->param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson($rel['code'],'签名验证失败');
        }
        $group = explode('/',$param['ids']);
        $adword = [];
        foreach ($group as $value) {
            $adword[$value] = [];
        }
        $rel = GreenAdwords::where(['member_miniapp_id' => $this->miniapp_id,'group' => $group])->field('title,picture,link,open_type,group')->order('sort desc,id desc')->select();
        foreach ($rel as $rs) {
            $adword[$rs['group']][] = $rs;
        }
        return enjson(200,'成功',$adword);
    }
}