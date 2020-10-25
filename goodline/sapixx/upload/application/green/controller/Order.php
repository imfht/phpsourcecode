<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单管理
 */
namespace app\green\controller;
use think\facade\Request;
use app\green\model\GreenOrder;
use app\green\model\OrderCache;

class Order extends Common{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'订单管理','url'=>url("green/Order/index")]]);
    }

    /**
     * 订单库
     * @return void
     */
    protected function orderList(){
        $starttime = Request::param('starttime');
        $endtime   = Request::param('endtime');
        if(!empty($starttime) && !empty($endtime)){
            $starttime = strtotime(date('Y-m-d 00:00:00',strtotime($starttime)));
            $endtime   = strtotime(date('Y-m-d 23:59:59',strtotime($endtime)));
            if($starttime > $endtime){
                $this->error('开始日期不能大于结束日期');
            }
            if($endtime-$starttime >= 518401){
                $this->error('只支持连续6天的查找');
            }
        }
        $view['keyword'] = Request::param('keyword');
        $view['status']  = Request::param('status/d',0);
        $path[] = ['name'=>'订单管理','url'=>url("green/Order/index")];
        $condition = [];
        if(!empty($starttime) && !empty($endtime)){
            $condition[] = ['create_time','>=',$starttime];
            $condition[] = ['create_time','<=',$endtime];
        }
        if(!empty($view['keyword'])){
            $condition = [['order_no','=',trim($view['keyword'])]];
        }
        $condition[]        = ['member_miniapp_id', '=', $this->member_miniapp_id];
        $view['count']      = GreenOrder::where($condition)->count();
        switch ($view['status']) {
            case 1:
                $condition[] = ['paid_at','=',1];
                $condition[] = ['express_status','=',0];
                $condition[] = ['is_del','=',0];
                $path[] = ['name'=>'待发货','url'=>'javascript:;'];
                break;
            case 2:
                $condition[] = ['paid_at','=',1];
                $condition[] = ['express_status','=',1];
                $condition[] = ['is_del','=',0];
                $path[] = ['name'=>'已发货','url'=>'javascript:;'];
                break;
            case 3:
                $condition[] = ['is_del','=',0];
                $condition[] = ['paid_at','=',0];
                $path[] = ['name'=>'已发货','url'=>'javascript:;'];
                break;
            case 4:
                $condition[] = ['is_del','=',1];
                $path[] = ['name'=>'回收站','url'=>'javascript:;'];
                break;
            default:
                $condition[] = ['is_del','=',0];
                break;
        }
        $view['order']     = GreenOrder::where($condition)->order('id desc')->paginate(10);
        $view['starttime'] = empty($starttime) ? time() : $starttime;
        $view['endtime']   = empty($endtime) ? time() : $endtime;;
        $view['pathMaps']  = $path;
        return $view;
    }

    /**
     * 订单列表
     */
    public function index(){
        return view()->assign(self::orderList());
    }

    /**
     * 订单预览
     */
    public function view(){
        $view['order'] = GreenOrder::getOrder($this->request->param('order_no'),0,0);
        return view()->assign($view);
    }

    /**
     * 发货
     */
    public function sendgoods(){
        if(request()->isAjax()){
            $data = [
                'order_no'        => Request::param('order_no/s'),
                'express_company' => Request::param('express_company/s'),
                'express_no'      => Request::param('express_no/s'),
            ];
            $validate = $this->validate($data,'Order.sendgoods');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            //判断当前商品是否完成
            $result = GreenOrder::where(['paid_at' => 1,'order_no' => $data['order_no']])->find();
            if(empty($result)){
                return json(['code'=>0,'msg'=>'商品没有满足发货条件']);
            }
            $data['express_company']   = $data['express_company'];
            $data['express_no']        = $data['express_no'];
            $data['express_status']    = 1;
            $data['express_starttime'] = time();
            $rel = GreenOrder::where(['id' => $result->id])->update($data);
            if($rel){
                return enjson(200,'操作成功',['url' => url('green/Order/index',['order_no' => $data['order_no']])]);
            }
            return enjson(0);
        }else{
            $order_no = Request::param('order_no/s');
            $view['order'] = GreenOrder::getOrder($order_no,0,0);
            if(empty($view['order'])){
                $this->error("404 NOT FOUND");
            }
            $view['order_no'] = $order_no;
            return view()->assign($view);
        }
    }
   
    /**
     * 后台人工修改订单为已发货
     */
    public function paid(){
        $order_no = Request::param('order_no');
        $result = GreenOrder::where(['paid_at' => 0,'order_no' => $order_no])->update(['paid_at' => 1,'payment_id' => 0,'order_starttime' => time(),'paid_no' => 'GM'.order_no()]);
        if($result){
            return enjson(200,'操作成功',['url' => url('green/Order/index',['order_no' => $order_no,'status' => Request::param('status/d',0)])]);
        }
        return enjson(0);
    }

    /**
     * 删除
     */
    public function delete($order_no){
        $rel = GreenOrder::where(['order_no' => $order_no])->find();
        if($rel){
            if($rel['is_del'] == 0){
                $result = GreenOrder::update(['is_del'=>1],['order_no' => $order_no]);
            }else{
                $result = GreenOrder::where(['order_no' => $order_no])->delete();
            }
            if($result){
                return enjson(200,'操作成功',['url' => url('green/Order/index',['status' => Request::param('status/d',0)])]);
            }
        }
        return enjson(0);
    }

    /**
     * 清空回收站
     */
    public function alldelete(){
        $rel = GreenOrder::where(['is_del'=>1])->select();
        if($rel){
            foreach ($rel as $value) {
                GreenOrder::where(['order_no' => $value['order_no']])->delete();
            }
            return enjson(200,'操作成功',['url' => url('green/Order/index',['status' => Request::param('status/d',0)])]);
        }
        return enjson(0);
    }
}