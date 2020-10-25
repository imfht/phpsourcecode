<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 时间接口
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Times as AppTimes;

class Times extends Base{

    /**
     * 时间到货
     */
    public function index(){
        $data['index']   = $this->request->param('index',0);
        $data['time_id'] = $this->request->param('time_id',0);
        $data['sign']    = $this->request->param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $rel = AppTimes::field('id,name,start_time,end_time')->where(['member_miniapp_id' => $this->miniapp_id])->order('sort desc,id desc')->select();
        $h = date('H');
        $data = [];
        foreach ($rel as $key => $value) {
            $data[$key]['state'] = 0;
            $data[$key]['id']    = $value['id'];
            if($h <= $value['end_time']){
                if($h >= $value['start_time'] && $h <= $value['end_time']){
                    $data[$key]['name']       = $value['name'];
                    $data[$key]['state_text'] = '已开抢';
                    $data[$key]['state']      = 1;
                }else if($h <= $value['start_time']){
                    $data[$key]['name']       = $value['name'];
                    $data[$key]['state_text'] = '即将开始';
                }
            }else{
                $data[$key]['name']       = $value['name'];
                $data[$key]['state_text'] = '进行中'; 
            }
        }
        return enjson(200,'成功',$data);
    }  
}