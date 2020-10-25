<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 委托管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;
use util\Util;

class Entrust extends Manage{

    public function initialize()    {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'委托商品','url'=>'javascript:;']]);
    }

    /**
     *  成交列表
     * @return void
     */
    public function index(int $types = 0){
        if(!model('auth')->getAuth($this->user->id,2)){
            $this->error('无权限,你非【订单管理员】');
        }
        $keyword = trim(input('get.keyword','','htmlspecialchars'));
        $view['keyword'] = input('get.keyword');
        $view['types']   = $types;
        $condition['system_user.member_miniapp_id'] = $this->member_miniapp_id;   
        if(!empty($keyword)){
            $condition['system_user.phone_uid'] = $keyword;     
        }
        $lists = model('Entrust')->giftManagelist($condition,$types,$keyword);
        $data = [];
        foreach ($lists as $key => $value) {
            $data[$key] = $value;
            $data[$key]['entrust_price'] = money($value['entrust_price']/100);
            $data[$key]['rebate'] = money($value['rebate']/100);
            $data[$key]['howday'] = util::ftime($value['create_time']);
        }
        $view['on_under']  = model('EntrustList')->where(['is_rebate' => 0,'is_under' => 0])->count();
        $view['off_under'] = model('EntrustList')->where(['is_rebate' => 0,'is_under' => 1])->count();
        $view['on_rebate']  = model('EntrustList')->where(['is_rebate' => 0,])->count();
        $view['off_rebate'] = model('EntrustList')->where(['is_rebate' => 1,])->count();
        $view['diy'] = model('EntrustList')->where(['is_rebate' => 1,'is_diy' => 1])->count();
        $view['lists'] = $data;
        $view['pages'] = $lists->render();;
        return view('order/entrust')->assign($view);
    }

    /**
     * 成交管理
     * @return void
     */
    public function lists(){
        if(!model('auth')->getAuth($this->user->id,3)){
            $this->error('无权限,你非【财务管理员】');
        }
        $condition['fastshop_entrust.member_miniapp_id'] = $this->member_miniapp_id;   
        $view['lists'] = model('Entrust')->entrustList($condition);
        $view['page']  = input('?get.page') ? input('get.page/d') : 0;
        return view('order/lists')->assign($view);
    }
    
    /**
     * 自动成交
     * @return void
     */
    public function isgift(int $item_id,int $page){
        if($this->user->parent_id){
            return json(['code'=>0,'msg'=>'无权限,非【创始人】身份']);
        }
        $config = model('Config')->where(['member_miniapp_id' => $this->member_miniapp_id])->find();
        $rebate = widget('order/rebate',['miniapp_id' => $this->member_miniapp_id, 'order_no' => 0,'item_id' => $item_id,'uid' =>0,'config' => $config]);
        if($rebate){
            return json(['code'=>200,'msg'=>'成交成功','data' => ['url' => url('entrust/lists',['page' => $page])]]);
        }
        return json(['code'=>0,'msg'=>'成交失败']);
    }

    /**
     * 指定成交
     * @return void
     */
    public function usergift(){
        if($this->user->parent_id){
            return json(['code'=>0,'msg'=>'无权限,非【创始人】身份']);
        }
        if(request()->isAjax()){
            $data = [
                'item_id'           => input('post.item_id/d'),
                'phone_id'          => input('post.phone_id/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'Order.sendgift');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $rebate_user = model('SystemUser')->where(['phone_uid' => $data['phone_id'],'member_miniapp_id' => $this->member_miniapp_id])->find();
            if(empty($rebate_user)){
                return json(['code'=>0,'msg'=>'未找到用户']);
            }
            $config = model('Config')->where(['member_miniapp_id' => $this->member_miniapp_id])->find();
            $rebate = widget('order/rebate',['miniapp_id' => $this->member_miniapp_id, 'order_no' => 0,'item_id' => $data['item_id'],'uid' =>$rebate_user->id,'config' => $config]);
            if($rebate){
                return json(['code'=>200,'msg'=>'成交成功','data' => ['url' => url('entrust/lists',['page' => input('post.page/d')])]]);
            }else{
                return json(['code'=>0,'msg'=>'未找到要对的单']);
            }
        }else{
            $view['item_id'] = input('get.item_id/d');
            $view['page']    = input('get.page/d');
            return view('order/usergift')->assign($view);
        }
    }
    
    /**
     * 数据校准
     * @return void
     */
    public function giftcount(int $item_id,int $page){
        $entrust = model('EntrustList')->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 0,'item_id' => $item_id])->count();
        model('entrust')->where(['member_miniapp_id' => $this->member_miniapp_id,'item_id' => $item_id])->update(['gite_count' => $entrust]);
        return json(['code'=>200,'msg'=>'校准成功','data' => ['url' => url('entrust/lists',['page' => $page])]]);
    }

    /**
     * 置顶/取消
     * @param integer $id 用户ID
     */
    public function isUnder(int $id){
        $info = model('EntrustList')->where(['id' => $id])->field('id,is_under,is_rebate')->find();
        if($info->is_rebate){
            return json(['code'=>0,'message'=>'已成交,禁止修改']);
        }
        $data['is_under'] = $info->is_under ? 0 : 1;
        $result = model('EntrustList')->where(['id' => $info->id])->update($data);
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    } 
}