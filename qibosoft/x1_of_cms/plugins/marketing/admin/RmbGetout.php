<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use plugins\marketing\model\RmbGetout as RmbGetoutModel;
use plugins\marketing\model\RmbConsume as RmbConsumeModel;
use app\common\util\Weixin;


//人民币提现
class RmbGetout extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
			'page_title'=>'会员提现管理',
	        'top_button'=>[ ['type'=>'delete']],	       
// 	        'hidden_edit'=>true,	
	];
	
	protected function _initialize()
	{
		parent::_initialize();
		$this->model = new RmbGetoutModel();
		$this->list_items = [
		        ['posttime', '申请日期', 'datetime'],
		        ['username', '会员帐号', 'link',get_url('user',['uid'=>'__uid__']),'_blank'],	
				['money', '提现金额', 'text'],
		        ['banktype', '收款方式', 'text'], 
				['ifpay', '支付与否', 'yesno'],
				['id', '拒绝提现', 'callback',function($id,$rs){
				    if($rs['ifpay']==1){
				        return '已提现';
				    }elseif($rs['ifpay']==-1){
				        return '已拒绝';
				    }
				    return "<a onclick=\"layer.prompt({ title: '请输入拒绝原因',formType: 0}, function(value){ window.location.href='".purl('refuse',['id'=>$id])."?why='+value; })\"><i class='glyphicon glyphicon-log-in'><i></a>";
				}],				
			];
		$this->tab_ext['right_button'] = [
		        ['type'=>'delete'],
		        [
		                'title'=>'收入明细',
		                'icon'=>'fa fa-list-ol',
		                'href'=>auto_url('log',['uid'=>'__uid__']),
		        ],
		        [
		                'title'=>'微信付款',
		                'icon'=>'fa fa-wechat',
		                'href'=>auto_url('pay',['id'=>'__id__','type'=>'weixin']),
		        ],
		        [
		                'title'=>'线下已付',
		                'icon'=>'fa fa-cc-paypal',
		                'href'=>auto_url('pay',['id'=>'__id__','type'=>'other']),
		        ],
		];
		$this->tab_ext['search'] = [
		        'uid'=>'用户UID',
		];
		$this->tab_ext['help_msg'] = '1、只能删除已拒绝的申请记录，未付款或已付款的记录不能删除<br>2、拒绝申请提现的话,金额会原路返回到帐户余额。<br>3、微信付款需要开通微信认证服务号的商户支付零钱到个人微信功能,并且要往里边充值足够的金额(注意T+1的微信商户号开通条件有严格要求)';
	}
	
	/**
	 * 删除没做操作的记录
	 * @param unknown $ids
	 */
	public function delete($ids = null) {
	    $ids = is_array($ids)?:[$ids];
	    foreach($ids AS $id){
	        $info = RmbGetoutModel::get($id);
	        if ($info['ifpay']!=-1) {
	            $this->error('只能删除已拒绝的申请记录!未付款或已付款的记录不能删除');
	        }
	    }
	    if ($this -> deleteContent($ids)) {
	        $this -> success('删除成功');
	    } else {
	        $this -> error('删除失败');
	    }
	}
	
	/**
	 * 查看用户收入明细
	 * @param number $uid
	 * @return mixed|string
	 */
	public function log($uid=0){
	    $this->list_items = [
	            ['posttime', '发生日期', 'datetime'],
	            ['money', '发生金额', 'text'],
	            ['about', '附注说明', 'text'],
	    ];
	    $this->tab_ext = [
	            'page_title'=>'查看用户收入明细',
	            'top_button'=> [['type'=>'back']],
	            'right_button'=>[],	        
	    ];
	    $data_list = RmbConsumeModel::where(['uid'=>$uid])->order('id desc')-> paginate(20);
	    return $this->getAdminTable($data_list);
	}
	
	/**
	 * 拒绝提现
	 * @param number $id
	 * @param string $why 原因
	 */
	public function refuse($id=0,$why=''){
	    $info = RmbGetoutModel::get($id);
	    if (empty($info)) {
	        $this->error('信息不存在');
	    }
	    if ($info['ifpay']==1) {
	        $this->error('已经给用户支付过了!拒绝无效');
	    }elseif ($info['ifpay']==-1) {
	        $this->error('已拒绝提现,不能再拒绝!');
	    }
	    $user = get_user($info['uid']);
	    if(empty($user)){
	        $this->error('用户资料不存在!');;
	    }
	    //$money = $info['money'];
	    $real_money = $info['real_money']?:$info['money']; //实际申请金额
	    add_rmb($user['uid'],0,-$real_money,'拒绝提现,解除冻结金额');
	    add_rmb($user['uid'],$real_money,0,'拒绝提现,退回提现金额');
	    $title = '很抱歉,你的提现被拒绝了';
	    $content = '很抱歉,你申请的提现被 '.$this->user['username'].' 拒绝了，金额已原路返回到你的帐户余额，原因如下:'.$why;
	    send_msg($user['uid'],$title,$content);
	    send_wx_msg($user['uid'], $content);
	    $data = [
	        'id'=>$id,
	        'ifpay'=>-1,
	        'admin'=>$this->user['username'],
	        'why'=>$why,
	        'replytime'=>time(),
	    ];
	    $result = RmbGetoutModel::update($data);
	    if ($result) {
	        $this->success('操作成功');
	    }else{
	        $this->error('数据库执行失败!');
	    }
	}
	
	/**
	 * 付款
	 * @param number $id
	 * @param string $type
	 */
	public function pay($id=0,$type=''){
	    $info = RmbGetoutModel::get($id);
	    if (empty($info)) {
	        $this->error('信息不存在');
	    }
	    if ($info['ifpay']==1) {
	        $this->error('已经给用户支付过了!');;
	    }elseif ($info['ifpay']==-1) {
	        $this->error('已拒绝提现,不能再支付!');;
	    }
	    $user = get_user($info['uid']);
	    if(empty($user)){
	        $this->error('用户资料不存在!');;
	    }
	    
	    $money = $info['money'];
	    $real_money = $info['real_money']?:$info['money']; //实际申请金额
	    
	    if($type=='weixin'){
	        if (empty($user['weixin_api'])) {
	            $this->error('该用户没有绑定过微信!');;
	        }
	        if($money<0.3){
	            $this->error('微信转帐不能小于0.3元!');;
	        }
	    }
	    
	    $data = [
	        'id'=>$id,
	        'ifpay'=>1,
	        'admin'=>$this->user['username'].' '.$type,
	        'replytime'=>time(),
	    ];
	    $result = RmbGetoutModel::update($data);
	    
	    if ($result) {
	        $url = ',<a href="'.get_url(purl('marketing/rmb/index',[],'member')).'" target="_blank">点击查看详情</a>';
	        if($type=='weixin'){
	            $array = [
	                'money'=>$money,
	                'title'=>'提现成功',
	                'id'=>$user['weixin_api'],
	            ];
	            $res = Weixin::gave_moeny($array);
	            if($res===true){
	                add_rmb($user['uid'],0,-$real_money,'微信提现成功');
	                send_wx_msg($user['weixin_api'], "你申请的提现 {$money} 元,已审核通过,并且已成功转帐,请注意查收".$url);
	            }else{
	                $data['ifpay'] = 0;
	                RmbGetoutModel::update($data);
	                $this->error('微信支付失败:'.$res);
	            }
	        }else{
	            add_rmb($user['uid'],0,-$real_money,'提现成功,扣除冻结金额');
	            send_msg($user['uid'],'提现转帐提醒',"你申请的提现 {$money} 元,已审核通过,线下已转帐,请注意查收".$url);
	        }
	        $this->success('操作成功');
	    }else{
	        $this->error('数据库执行失败!');
	    }
	}

}
