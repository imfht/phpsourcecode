<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase;

use app\common\traits\AddEditList;

use plugins\marketing\model\Moneytype as MoneytypeModel;

//虚拟币种类
class Moneytype extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
	    'page_title'=>'虚拟币种类',
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new MoneytypeModel();
		
		$this->form_items = [
		        ['text','name','币种名称'],
		        ['icon','icon','图标'],
		];
		cache('money_types',null);
	}
	
	public function index() {
	    $this->tab_ext['right_button'] = [
	            ['type'=>'delete'],
	            ['type'=>'edit','title'=>'兑换积分比例','icon'=>'fa fa-gears'],
	    ];
	    $this->list_items = [
	            ['icon', '图标', 'icon'],
	            ['name', '名称', 'text.edit'],
	            ['list', '排序值', 'text.edit'],
	            //['bank1', '收款帐号', 'text'],
	    ];
	    $listdb = $this->getListData($map = [], $order ='list desc,id asc');
	    return $this -> getAdminTable($listdb);
	}
	
	public function edit($id=0){
	    if (empty($id)) $this -> error('缺少参数');
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        $this->request->post([
	                'more_ratio'=>json_encode([
	                        'user'=>$data['user'],
	                        'types'=>$data['types'],
	                ]),
	        ]);
	    }
	    $info = $this -> getInfoData($id);
	    $array = json_decode($info['more_ratio'],true);
	    $info['user'] = $array['user'];
	    $info['types'] = $array['types'];
	    $this->tab_ext['page_title'] = '100个 '.$info['name'].' 可以兑换成多少个积分（如果100个 '.$info['name'].' 只能兑换20个积分,那么就需要满5个 '.$info['name'].'才能申请兑换），下面各项是累加的';
	    $array = [
	            ['icon','icon','图标'],
	            ['usergroup','group_ratio','不同用户组分别可以兑换多少个'],
	            ['number','user[weixin_api]','绑定微信登录的可兑换多少个'],
	            ['number','user[wx_attention]','关注微信公众号的可兑换多少个'],
	            ['number','user[email_yz]','验证过邮箱的可兑换多少个'],
	            ['number','user[mob_yz]','验证过手机的可兑换多少个'],
	            ['number','user[idcard_yz]','审核过身份证件的可兑换多少个'],
	            ['textarea','user[regdate]','根据不同注册时间分别可兑换多少个','比如注册5个月以上可以兑换10个,注册10个月以上可以兑换30个,就输入<br><font color="blue">5|10<br>10|30</font><br>多个不同时长就换行处理'],
	            ['textarea','user[addrmb]','根据不同充值消费金额分别可兑换多少个','比如曾消费100元以上可以兑换5个,曾消费200元以上可以兑换15个,就输入<br><font color="blue">100|5<br>200|15</font><br>多个换行处理'],
	            ['textarea','user[uid]','根据不同的UID用户分别可兑换多少个','比如UID为2的用户可以兑换5个,UID为3的用户可以兑换15个,就输入<br><font color="blue">2|5<br>3|15</font><br>多个换行处理，特别提醒还可以设置为负数作为惩罚'],
	    ];
	    if(modules_config('bbs')){
	        $array[]=['textarea','user[addtopic]','根据不同发表论坛主题数分别可兑换多少个','比如发主题100篇以上可以兑换5个,比如发主题200篇以上可以兑换15个,就输入<br><font color="blue">100|5<br>200|15</font><br>多个换行处理'];
	        $array[]=['textarea','user[addreply]','根据不同回贴数分别可兑换多少个','比如回贴数100条以上可以兑换5个,比如回贴数200条以上可以兑换15个,就输入<br><font color="blue">100|5<br>200|15</font><br>多个换行处理'];
	    }
	    $this->form_items = array_merge($array,$this->get_types($id));
	    return $this -> editContent($info);
	}
	
	private function get_types($id=0){
	    $array = [];
	    foreach(jf_name() AS $key=>$name){
	        if($key!=0 && $key!=$id){
	            $array[] = ['textarea','types['.$key.']',"根据不同 {$name} 分别可兑换多少个","比如{$name}100个以上可以兑换5个,比如{$name}200个以上可以兑换15个,就输入<br><font color='blue'>100|5<br>200|15</font><br>多个换行处理"];
	        }
	    }
	    return $array;
	}
	
}
