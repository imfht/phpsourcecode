<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;

class Order extends Manage{

    public function initialize() {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,2)){
            $this->error('无权限,你非【订单管理员】');
        }
        $this->assign('pathMaps',[['name'=>'订单管理','url'=>url("fastshop/order/index")]]);
    }

    /**
     * 订单列表
     */
    public function index(){
        $starttime = empty(input('get.starttime')) ? 0 : strtotime(input('get.starttime/s'));
        $endtime   = empty(input('get.endtime')) ? 0 : strtotime(input('get.endtime/s'));
        if(!empty($starttime) && !empty($endtime)){
            if($starttime > $endtime){
                $this->error('开始日期不能大于结束日期');
            }
            if($endtime-$starttime >= 518401){
                $this->error('只支持连续6天的查找');
            }
            $starttime = strtotime(date('Y-m-d 00:00:00',$starttime));
            $endtime   = strtotime(date('Y-m-d 23:59:59',$endtime));
        }
        $view['keyword'] = input('get.keyword');
        $view['status']  = input('status/d');
        $path[] = ['name'=>'订单管理','url'=>url("fastshop/order/index")];
        switch ($view['status']) {
            case 1:
                $condition = [['paid_at','=',0],['is_del','=',0]];
                $path[] = ['name'=>'未付款','url'=>'javascript:;'];
                break;
            case 2:
                $condition = [['paid_at','=',1],['is_del','=',0]];
                $path[] = ['name'=>'已付款','url'=>'javascript:;'];
                break;
            case 3:
                $condition = [['paid_at','=',1],['express_status','=',1],['is_del','=',0]];
                $path[] = ['name'=>'已发货','url'=>'javascript:;'];
                break;
            case 4:
                $condition = [['is_del','=',1]];
                $path[] = ['name'=>'回收站','url'=>'javascript:;'];
                break;
            case 5:
                $condition = [['status','=',1],['is_del','=',0]];
                $path[] = ['name'=>'已结单','url'=>'javascript:;'];
                break;
            case 6:
                $condition = [['paid_at','=',1],['is_del','=',0],['express_status','=',0],['is_entrust','=',1]];
                $path[] = ['name'=>'已确认','url'=>'javascript:;'];
                break;  
            case 7:
                $condition = [['paid_at','=',1],['is_del','=',0],['express_status','=',0],['is_entrust','=',0]];
                $path[] = ['name'=>'未确认','url'=>'javascript:;'];
                break;
            default:
                $condition = [['is_del','=',0]];
                break;
        }
        if(!empty($view['keyword'])){
            $condition = [['fastshop_order.order_no','=',trim($view['keyword'])]];
        }
        $condition[] = ['fastshop_order.member_miniapp_id','=',$this->member_miniapp_id];
        $view['lists'] = model('Order')->getOrderList($condition,$view['status']);
        $view['order'] = model('Order')->order_data($view['lists']->toArray()['data']);
        $view['starttime'] = empty($starttime) ? time() : $starttime;
        $view['endtime']   = empty($endtime) ? time() : $endtime;;
        $view['pathMaps'] = $path;
        return view()->assign($view);
    }

    /**
     * 导出Excel
     * @return void
     */
    public function excel(){
        $status    = input('status/d');
        switch ($status) {
            case 1:
                $condition = [['paid_at','=',0],['is_del','=',0]];
                $path[] = ['name'=>'未付款','url'=>'javascript:;'];
                break;
            case 2:
                $condition = [['paid_at','=',1],['is_del','=',0]];
                $path[] = ['name'=>'已付款','url'=>'javascript:;'];
                break;
            case 3:
                $condition = [['paid_at','=',1],['express_status','=',1],['is_del','=',0]];
                $path[] = ['name'=>'已发货','url'=>'javascript:;'];
                break;
            case 4:
                $condition = [['is_del','=',1]];
                $path[] = ['name'=>'回收站','url'=>'javascript:;'];
                break;
            case 5:
                $condition = [['status','=',1],['is_del','=',0]];
                $path[] = ['name'=>'已结单','url'=>'javascript:;'];
                break;
            case 6:
                $condition = [['paid_at','=',1],['is_del','=',0],['express_status','=',0],['is_entrust','=',1]];
                $path[] = ['name'=>'已确认','url'=>'javascript:;'];
                break;  
            case 7:
                $condition = [['paid_at','=',1],['is_del','=',0],['express_status','=',0],['is_entrust','=',0]];
                $path[] = ['name'=>'未确认','url'=>'javascript:;'];
                break;
            default:
                $condition = [['is_del','=',0]];
                break;
        }
        $starttime = strtotime(input('get.starttime/s'));
        $endtime   = strtotime(input('get.endtime/s'));
        if(empty($starttime) || empty($endtime)){
            $this->error('没有输入导出范围');
        }else{
            $starttime = strtotime(date('Y-m-d 00:00:00',$starttime));
            $endtime   = strtotime(date('Y-m-d 23:59:59',$endtime));
            $condition[] = ['order_starttime','>=',$starttime];
            $condition[] = ['order_starttime','<=',$endtime];
        }
        if(input('?get.keyword')){
            $condition = [['fastshop_order.order_no','=',trim(input('get.keyword','','htmlspecialchars'))]];
        }
        $condition[] = ['fastshop_order.member_miniapp_id','=',$this->member_miniapp_id];
        header("Content-type: text/plain");
        header("Accept-Ranges: bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=order_".date('Y-m-d').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $view['lists'] = model('Order')->getOrderListNopage($condition,$status);
        $view['order'] = model('Order')->order_data($view['lists']->toArray());
        return view()->assign($view);
    }


    /**
     * 订单预览
     */
    public function view($order_no){
        $view['order'] = model('Order')->getOrder($order_no,0,0);
        return view()->assign($view);
    }

    /**
     * 发货
     */
    public function sendgoods(){
        if(request()->isAjax()){
            $data = [
                'order_no'        => input('post.order_no/d'),
                'express_company' => input('post.express_company/s'),
                'express_no'      => trim(input('post.express_no/s')),
            ];
            $validate = $this->validate($data,'Order.sendgoods');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //判断当前商品是否完成
            $result = model('Order')->where(['paid_at' => 1,'order_no' => $data['order_no']])->where('is_entrust','>',0)->find();
            if(empty($result)){
                return json(['code'=>0,'msg'=>'商品没有满足发货条件']);
            }
            $data['express_company']   = trim($data['express_company']);
            $data['express_no']        = trim($data['express_no']);
            $data['express_status']    = 1;
            $data['express_starttime'] = time();
            $rel = model('Order')->save($data,['paid_at' => 1,'order_no' => $data['order_no']]);
            if($rel){
                return json(['code'=>200,'data' => ['url' => url('fastshop/order/view',['order_no'=>$data['order_no']])],'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $order_no = input('get.order_no');
            $view['order'] = model('Order')->getOrder($order_no);
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
    public function paid(int $order_no){
        $data['paid_at']         = 1;
        $data['payment_id']      = 0;
        $data['order_starttime'] = time();
        $data['paid_no']         = 'GM'.order_no();
        $result = model('Order')->update($data,['paid_at' => 0,'order_no' => $order_no]);
        if($result){
            return json(['code'=>200,'data' => ['url' => url('fastshop/order/view',['order_no' => $order_no])],'msg'=>'操作成功']);
        }else{
            return json(['code'=>0,'msg'=>'操作失败']);
        }
    }

    /**
     * 删除
     */
    public function delete($order_no){
        $status = input('status/d');
        $rel = model('Order')->where(['order_no' => $order_no])->find();
        if($rel){
            if($rel['is_del'] == 0){
                $result = model('Order')->update(['is_del'=>1],['order_no' => $order_no]);
            }else{
                $result = model('Order')->where(['order_no' => $order_no])->delete();
                if($result){
                    model('OrderCache')->where(['order_no' => $order_no])->delete();
                }
            }
            if($result){
                return json(['code'=>200,'data' => ['url' => url('fastshop/order/index',['status' => $status])],'msg'=>'操作成功']);
            }
        }
        return json(['code'=>0,'msg'=>'操作失败']);
    }

    /**
     * 清空回收站
     */
    public function alldelete(){
        $status = input('status/d');
        $rel = model('Order')->where(['is_del'=>1])->select();
        if($rel){
            foreach ($rel as $key => $value) {
                model('Order')->where(['order_no' => $value['order_no']])->delete();
                model('OrderCache')->where(['order_no' => $value['order_no']])->delete();
            }
            return json(['code'=>200,'data' => ['url' => url('fastshop/order/index',['status' => $status])],'msg'=>'操作成功']);
        }
        return json(['code'=>0,'msg'=>'操作失败']);
    }

    /**
     * 确认收货
     */
    public function completion($order_no){
        $result = model('Order')->update(['status'=>1],['express_status'=>1,'order_no' => $order_no]);
        if($result){
            return json(['code'=>200,'data' => ['url' => url('fastshop/order/view',['order_no' => $order_no])],'msg'=>'操作成功']);
        }else{
            return json(['code'=>0,'msg'=>'操作失败']);
        }
    }

    /**
     * 订单完成
     */
    public function force_completion($order_no){
        $status = input('status/d');
        $result = model('Order')->update(['status'=>1],['order_no' => $order_no]);
        if($result){
            return json(['code'=>200,'data' => ['url' => url('fastshop/order/index',['status' => $status])],'msg'=>'操作成功']);
        }else{
            return json(['code'=>0,'msg'=>'操作失败']);
        }
    } 
}