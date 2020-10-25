<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应用管理
 */
namespace app\system\controller\passport;
use app\common\event\Passport;
use app\common\model\SystemMember;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMiniapp;
use app\common\model\SystemMemberMiniappOrder;
use app\common\model\SystemMemberBank;
use app\common\model\SystemMemberBankBill;

class Appshop extends Common{

    public function initialize(){
        parent::initialize();
        if($this->user->parent_id){
            $this->error('仅创始人有权限访问');
        }
        $this->assign('pathMaps', [['name'=>'应用商店','url'=>url('system/passport.appshop/index')]]);
    }

   /* 列表
    * @access public
    */
   public function index(){
       $view['list']  = SystemMiniapp::where(['is_lock' => 0,'is_diyapp' => 0])->order('id desc')->paginate(10);
       return view()->assign($view);
   }
   
   /**
    * 阅读内容
    * @return void
    */
   public function review(int $id){
        $view['info']  = SystemMiniapp::where(['id' => $id,'is_lock' => 0,'is_diyapp' => 0])->find();
        if(!$view['info']){
            return $this->error("404 NOT FOUND");
        }
        $view['style_pic'] =  empty($view['info']['style_pic']) ? [] :json_decode($view['info']['style_pic'],true);
        $view['miniapp']   =  SystemMemberMiniappOrder::where(['member_id' => $this->user['id'],'miniapp_id' => $id])->count(); 
        $view['pathMaps'] =  [['name'=>'应用商店','url'=>url('system/passport.appshop/index')],['name'=>$view['info']['title'],'url'=>url('passport.appshop/review',['id' => $id])]];
        return view()->assign($view);
   }

   /**
    * 购买小程序
    */
   public function buy($input){
        if(request()->isPost()){
            $data = [
                'member_id'    => $this->user->id,
                'id'           => $this->request->param('miniapp_id/d'),
                'title'        => $this->request->param('appname/s'),
                'safepassword' => $this->request->param('safepassword/s'),
            ];
            $validate = $this->validate($data,'MemberBank.buy');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //判断安全密码是否正确
            $isPass = empty($this->user->safe_password) ? SystemMember::checkPasspord($this->user->id,$data['safepassword']) : SystemMember::checkSafePasspord($this->user->id,$data['safepassword']);
            if(!$isPass){
                return enjson(0,'密码不正确');
            }
            $miniapp  = SystemMiniapp::where(['id' => $data['id'],'is_lock' => 0])->field('id,sell_price,template_id')->find();
            if(empty($miniapp)){
                return json(['code'=>0,'msg'=>'未找到应用']);
            }
            //如果价格<=0，就不在查询数据库
            if($miniapp->sell_price > 0){
                $rel = SystemMemberBank::moneyJudge($this->user->id,$miniapp->sell_price);
                if($rel){
                    return json(['code'=>0,'msg'=>'余额不足,请充值']);
                }
            }
            //新增购买列表
            $order['member_id']  = (int)$data['member_id'];
            $order['miniapp_id'] = (int)$data['id'];
            $order['update_var'] = (int)$miniapp->template_id;
            $order['start_time'] = time();
            $order['end_time']   = time() + 31536000;
            //创建订单、账单、扣费
            $id = SystemMemberMiniappOrder::insertGetId($order);
            SystemMemberBank::moneyUpdate($this->user->id,-$miniapp->sell_price);
            SystemMemberBankBill::create(['state' => 1,'money' => $miniapp->sell_price,'member_id' => $this->user->id,'message' => '购买应用程序' . $data['title'],'update_time' => time()]);
            //同步创建客户应用并生成服务ID
            $member_miniapp_id = SystemMemberMiniapp::insertGetId(['miniapp_order_id' => $id,'member_id' => $this->user->id,'miniapp_id' => $data['id'],'appname' => $data['title'],'create_time' => time()]);
            SystemMemberMiniapp::where(['id' => $member_miniapp_id])->update(['service_id' => uuid(3,true,$member_miniapp_id)]); //生成应用服务ID
            //设置当前管理的应用ID
            Passport::setMiniapp(['member_id' => $this->user->id,'miniapp_id' => $miniapp->id,'member_miniapp_id' => $member_miniapp_id]);  //设置登录
            return json(['code'=>200,'msg'=>'开通成功,授权你的公众号或小程序即可开始使用','parent' => 1]);
        }else{
            $view['info']  = SystemMiniapp::where(['id' => $input,'is_lock' => 0])->find();
            if(!$view['info']){
                return $this->error("404 NOT FOUND");
            }
            $view['bank']    = SystemMemberBank::where(['member_id' => $this->user->id])->find();
            $view['miniapp'] = SystemMemberMiniappOrder::where(['member_id' => $this->user->id,'miniapp_id' => $input])->count(); 
            return view()->assign($view);
        }
   }
}