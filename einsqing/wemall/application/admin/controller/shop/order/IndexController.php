<?php
namespace app\admin\controller\shop\order;
use app\admin\controller\BaseController;

class IndexController extends BaseController
{
	//订单列表
	public function index(){
		$map = array();
        $search = '?';
		if(input('param.payment_id') != '' && input('param.payment_id') != '-10'){
            $map['payment_id']  = input('param.payment_id');
            $search .= 'payment_id='.input('param.payment_id').'&';
        }
        if(input('param.pay_status') != '' && input('param.pay_status') != '-10'){
            $map['pay_status']  = input('param.pay_status');
            $search .= 'pay_status='.input('param.pay_status').'&';
        }
        if(input('param.status') != '' && input('param.status') != '-10'){
            $map['status']  = input('param.status');
            $search .= 'status='.input('param.status').'&';
        }
        if(input('param.name') != ''){
            $ids = model('OrderContact')->where('name|phone','like', '%'.input('param.name').'%')->column('order_id');
            if($ids){
            	$map['id']  = ['in',implode(",",$ids)];
            }else{
            	$map['orderid']  = ['like','%'.input('param.name').'%'];
            }
            $search .= 'name='.input('param.name').'&';
        }
        if(input('param.timeRange') != ''){
        	$timeRange = explode(" --- ", input('param.timeRange'));
        	$map['created_at']  = ['between',$timeRange];
            $search .= 'timeRange='.input('param.timeRange').'&';
        }

        if(input('param.day') != ''){
            $map['created_at']  = ['like',input('param.day').'%'];
            $search .= 'day='.input('param.day').'&';
        }

		$orderlist = model('Order')->with('user,contact,detail')->order('id desc')->where($map)->paginate();

        $page = str_replace("?",$search,$orderlist->render());
        $this->assign("page", $page);

		cookie("prevUrl", request()->url());

		$paymentlist = model('Payment')->select()->toArray();
		$this->assign('paymentlist', $paymentlist);
		$this->assign('condition', input('param.'));
		$this->assign('orderlist', $orderlist);
		return view();
	}

    //订单详情
    public function detail(){
        if (request()->isPost()){
            $data = input('post.');
            if(input('post.id')){
                $result = model('Order')->update($data);
            }else{
                $result = model('Order')->create($data);
            }
            if($data['status'] == 1){
                // 发货模版消息
                action('admin/WechatController/sendTplMsgOrderPublish',['order_id' => $data['id']]);
            }

            if($result){
                $this->success("保存成功", cookie("prevUrl"));
            }else{
                $this->error('保存失败', cookie("prevUrl"));
            }
        }else{
            $id = input('param.id');
            if($id){
                $order = model('Order')->with('user,contact,delivery,detail.product.file,fee')->find($id);
                
                $order_r = $order->toArray();
                $order_r['pay_status'] = $order->getData('pay_status');
                $order_r['status'] = $order->getData('status');
 
                $this->assign('order', $order_r);
            }
            //快递列表
            $deliverylist = model('Delivery')->where('status',1)->select();
            $this->assign('deliverylist', $deliverylist);
            // halt($order->toArray());
            return view();
        }
    }

    //改变订单状态
    public function update(){
        $data = input('param.');
        if(input('param.status')){
            $result = model('Order')->where('id','in',$data['id'])->update(['status' => $data['status']]);
           
            //退款操作
            if($data['status'] == -3){
                $orders = model('Order')->where('id','in',$data['id'])->select();
                foreach ($orders as &$order) {
                    //用户余额增加
                    model("User")->where('id', $order['user_id'])->setInc('money', $order['totalprice']);
                }
            }
        }
        if(input('param.pay_status')){
            $result = model('Order')->where('id','in',$data['id'])->update(['pay_status' => $data['pay_status']]);
        }
        if($result){
            $this->success("修改成功", cookie("prevUrl"));
        }else{
            $this->error('修改失败', cookie("prevUrl"));
        }
    }


    //导出全部订单
    public function export(){
        $map = array();
        if(input('param.id') != ''){
            $map['id']  = ['in',input('param.id')];
        }
        $orderlist = model('Order')->with('user,contact,detail')->where($map)->select()->toArray();
        $data = array(
            '0' => array(
                '1' => '编号',
                '2' => '订单编号',
                '3' => '用户',
                '4' => '联系人',
                '5' => '总金额',
                '6' => '积分',
                '7' => '支付方式',
                '8' => '支付状态',
                '9' => '订单状态',
                '10' => '订单详情',
                '11' => '下单时间',
            ),
        );
        foreach ($orderlist as &$v) {
            $detail = '';
            foreach ($v['detail'] as &$d) {
                $detail .= '['.$d['name'].'¥'.$d['price'].'*'.$d['num'].']';
            }
            array_push($data, array(
                '1' => $v['id'],
                '2' => $v['orderid'],
                '3' => $v['user']['username'],
                '4' => $v['contact']['name'].'-'.$v['contact']['phone'].'['.$v['contact']['province'].'-'.$v['contact']['city'].'-'.$v['contact']['district'].'-'.$v['contact']['address'].']',
                '5' => $v['totalprice'],
                '6' => $v['totalscore'],
                '7' => $v['payment'],
                '8' => $v['status'],
                '9' => $v['pay_status'],
                '10' => $detail,
                '14' => $v['created_at'],
            ));
        }
        export_to($data,'全部订单');//导出excle
    }

}