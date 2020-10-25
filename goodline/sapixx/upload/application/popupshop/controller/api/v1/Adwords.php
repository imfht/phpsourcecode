<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\popupshop\model\Adwords as Ads;
use think\facade\Request;
use think\facade\Validate;
use filter\Filter;

class Adwords extends Base{

    
    /**
     * 读取微信API
     * @param integer 读取ID
     * @return json
     */
    public function index($group = 1){
        $lists = Ads::field('title,picture,open_type,link')->where(['group_id'=> $group,'member_miniapp_id' => $this->miniapp_id])->order('sort desc,id desc')->select(); 
        $data = [];
        switch ($group) {
            case 1:
                $style = "?x-oss-process=style/640";
                break;
            case 2:
                $style = "?x-oss-process=style/640";
                break; 
            case 3:
                $style = "?x-oss-process=style/80";
                break;
            default:
                $style = "";
                break;
        }
        foreach($lists as $key => $rs){
            $data[$key]['title']     = $rs['title'];
            $data[$key]['images']    = $rs['picture'].$style;
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
        $ids = ids(explode('/',Request::param('apis','0')),true);
        $where = [];
        $where['member_miniapp_id'] = $this->miniapp_id;
        $where['group_id']          = $ids;
        $rel = Ads::where($where)->field('title,picture,link,open_type,group_id')->order('sort desc,id desc')->select();
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