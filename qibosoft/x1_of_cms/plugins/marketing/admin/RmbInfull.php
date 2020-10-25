<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;

use plugins\marketing\model\RmbInfull as RmbInfullModel;

//人民币充值
class RmbInfull extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
	    'page_title'=>'人民币充值管理',
	    'top_button'=>[['type'=>'delete']],
	    'right_button'=>[ ['type'=>'delete']],
	    'search'=>[
	        'uid'=>'用户UID',
	        'numcode'=>'订单号',
	        'money'=>'充值金额',
	    ],
	];
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new RmbInfullModel();
		$this->list_items = [
				['uid', '用户名', 'username',],
                ['money', '充值金额(元)', 'text'],
				['ifpay', '支付与否', 'callback', function($value,$rs){
				    $url = purl('pay',['id'=>$rs['id']]);
                    return $value>0 ? '<span style="color:red">付款成功</span>' : '<span style="color:blue">付款失败</span>'
                        ." <a href='{$url}'><i class='fa fa-check'></i>充值</a>";
                }],
				['posttime', '支付时间', 'datetime'],
				['numcode', '支付单号', 'text'],
                ['banktype', '支付方式', 'text'],
				//['bank1', '收款帐号', 'text'],
                
			];
	}
	
	public function pay($id=0){
	    $info = getArray($this->model->get($id));
	    if ($info['ifpay']==1) {
	        $this->error('当前订单已经支付成功了!');
	    }
	    $result = $this->model->update([
	        'id'=>$id,
	        'ifpay'=>1,
	    ]);
	    if($result){
	        $url = get_url(purl('rmb/index',[],'member'));
	        $title = '充值订单未及时到账,修复成功通知';
	        $content = "你于 ".date('Y-m-d H:i',$info['posttime'])." 充值的订单,未及时到账,管理员已成功修复,金额 {$info['money']} 元已存入你的帐户余额,<a href=\"{$url}\" target=\"_blank\">点击查看详情</a>";
	        add_rmb($info['uid'],$info['money'],0,'管理员修复充值订单');
	        send_msg($info['uid'],$title,$content);
	        send_wx_msg($info['uid'], $content);
	        $this->success('处理成功');
	    }else{
	        $this->error('处理失败!');
	    }
	}
	
	protected function getOrder($extra_order = '', $before = false)
	{
	    $order = parent::getOrder($extra_order, $before);
	    
	    if($order == ''){
	        return ' id desc ';
	    }else{
	        return $order;
	    }
	}
}
