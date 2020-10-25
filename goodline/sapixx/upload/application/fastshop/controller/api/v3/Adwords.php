<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Banner;
use think\facade\Request;

class Adwords extends Base{

    
    /**
     * 读取微信API
     * @param integer 读取ID
     * @return json
     */
    public function index(){
        $data['group'] = Request::param('group/d',1);
        $data['sign']  = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $lists = Banner::field('title,picture,open_type,link')->where(['group_id'=> $data['group'],'member_miniapp_id' => $this->miniapp_id])->order('sort desc,id desc')->select(); 
        $data = [];
        foreach($lists as $key => $rs){
            $data[$key]['title']     = $rs['title'];
            $data[$key]['images']    = $rs['picture'];
            $data[$key]['link']      = dehtml($rs['link']);
            $data[$key]['open_type'] = $rs['open_type'];
        }
        return json(['code'=>200,'msg'=>'成功','data' => $data]);
    } 
    
    /**
     * 获取内容
     * @param [type] $apis
     * @param integer $num
     * @return void
     */
    public function all(){
        $data['apis'] = Request::param('apis');
        $data['sign'] = Request::param('sign');
        $rel = $this->apiSign($data);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $ids = ids(json_decode(Request::param('apis')),true);
        $where = [];
        $where['member_miniapp_id'] = $this->miniapp_id;
        $where['group_id']          = $ids;
        $rel = Banner::where($where)->field('title,picture,link,open_type,group_id')->order('sort desc,id desc')->select();
        $cate = [];
        foreach ($ids as $value) {
            $cate[$value] = [];
        }
        foreach ($rel as $rs) {
            $cate[$rs['group_id']][] = $rs;
        }
        return json(['code'=>200,'msg'=>'成功','data' => $cate]);
    }
}