<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应用管理首页
 */
namespace app\fastshop\controller\Manage;
use app\common\controller\Manage;

class Index extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'管理首页','url'=>'javascript:;']]);
    }

    /**
     * 应用管理首页
     * @access public
     */
    public function Index(){
        $starttime = empty(input('get.starttime')) ? 0 : strtotime(input('get.starttime/s'));
        $endtime   = empty(input('get.endtime')) ? 0 : strtotime(input('get.endtime/s'));
        $whereorder = [];
        $entrust    = [];
        $rebate     = [];
        if(!empty($starttime) && !empty($endtime)){
            if($starttime > $endtime){
                $this->error('开始日期不能大于结束日期');
            }
            $whereorder[] = ['order_starttime','>=',$starttime];
            $whereorder[] = ['order_starttime','<=',$endtime];
            $entrust[]    = ['create_time','>=',$starttime];
            $entrust[]    = ['create_time','<=',$endtime];  
            $rebate[]     = ['update_time','>=',$starttime];
            $rebate[]     = ['update_time','<=',$endtime];
        }
        $view['order']           = model('order')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1])->count();
        $view['express']         = model('order')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'express_status' => 1])->count();
        $view['entrust']         = model('EntrustList')->where($entrust)->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 0])->count();
        $view['entrust_rebate']  = model('EntrustList')->where($rebate)->where(['member_miniapp_id' => $this->member_miniapp_id,'is_rebate' => 1])->count();
        $view['shopping']        = model('shopping')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'paid_at' => 1])->count();
        $view['shoppingexpress'] = model('shopping')->where($whereorder)->where(['member_miniapp_id' => $this->member_miniapp_id,'express_status' => 1])->count();
        $view['agent']           = model('Agent')->where(['member_miniapp_id' => $this->member_miniapp_id])->count();
        $view['countuser']      =  model('SystemUser')->where(['member_miniapp_id' => $this->member_miniapp_id])->count();
        $view['starttime']       = $starttime;
        $view['endtime']         = $endtime;
        return view('/manage/index')->assign($view);
    }    
}