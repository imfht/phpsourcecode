<?php
namespace plugins\marketing\admin;

use app\common\controller\AdminBase; 
use app\common\traits\AddEditList;
use app\common\model\User as UserModel;


/**
 * 用户管理
 */
class Member extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items;
	protected $list_items;
	protected $tab_ext = [
				'id'=>false,                //用户数据表非常特殊，没有用id而是用uid
				'page_title'=>'会员积分财务管理',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new UserModel();
	}
	
	/**
	 * 用户列表
	 */
	public function index() {
	    $order = 'uid desc';
	    $map = [];
	    $this->list_items = [
	        ['uid', '用户名', 'username'],
	        //['groupid', '用户组', 'select2',getGroupByid()],
	        ['rmb', '余额', 'text'],
	        ['rmb_freeze', '冻结余额', 'text'],
	        ['money', '积分', 'text'],	        
	    ];
	    
	    foreach(jf_name() AS $key=>$name){
	        if ($key==0) {
	            continue;
	        }
	        $this->list_items[] = ['id',$name, 'callback',function($v,$rs,$k){
	            return get_user_money($k,$rs['uid']);
	        },$key];
	    }
	    $this->list_items[] = ['id','明细表', 'callback',function($v,$rs){
	        $url = "<a href=\'".purl('rmb_consume/index')."?search_field=uid&keyword={$rs['uid']}\' style=\'color:#fff;\'>余额明细</a><br>";
	        foreach(jf_name() AS $key=>$name){
	            $url .= "<a href=\'".purl('moneylog/index')."?search_fields[uid]={$rs['uid']}&search_fields[type]={$key}\' style=\'color:#fff;\'>{$name}明细</a><br>";
	        }	        
	        $code = "<a href='#' class=\"fa fa-bars\" onclick=\"layer.tips('$url', $(this), {tips: [3, '#B9966E'],tipsMore: false,time:5000 });\"></a>";
	        return $code;
	    }];
	    
	    $this -> tab_ext['search'] = ['username'=>'用户名','uid'=>'用户ID','regip'=>'注册IP'];    //支持搜索的字段
	    $this -> tab_ext['order'] = 'money,rmb,uid,regdate,lastvist';   //排序选择
	    $this -> tab_ext['id'] = 'uid';    //用户数据表非常特殊，没有用id而是用uid ， 这里需要特别指定id为uid
	    
	    //筛选字段
// 	    $this -> tab_ext['filter_search'] = [ 
// 	            'yz'=>['未审核','已审核'],
// 	    ];
	    $this -> tab_ext['top_button'] = []; 
	    $this -> tab_ext['right_button'] = [
	            [
	                    'type'=>'edit',
	                    'title'=>'修改',
	                    'icon'=>'fa fa-edit'
	            ],
	    ];
	    
	    return $this -> getAdminTable(self::getListData($map, $order ));
	} 
	
	/**
	 * 修改用户资料
	 * @param number $id 用户UID
	 */
	public function edit($id = 0)
	{
	    if(empty($id)) $this->error('缺少参数');	    
    	
	    $info = $this->model->get_info($id);
	    
	    if ($this->request->isPost()) {    //修改入库处理
	        $data = $this->request->post();
	        $about = $this->user['username'].'操作 '.$data['about'];
	        
	        $_name = '';
	        
	        if(is_numeric($data['type'])){
	            add_jifen($data['uid'],$data['num'],$about,$data['type']);
	            $_name = jf_name($data['type']);
	            $_dw = '个';
	        }elseif($data['type']=='rmb'){
	            add_rmb($data['uid'],$data['num'],0,$about);
	            $_name = '余额';
	            $_dw = '元';
	        }elseif($data['type']=='rmb_freeze'){
	            add_rmb($data['uid'],0,$data['num'],$about);
	            $_name = '冻结余额';
	            $_dw = '元';
	        }else{
	            $this->error('未知类型');
	        }
	        $title = $_name.'变动通知';	        
	        
	        $content = '你的 '.$_name.' '.($data['num']>0?'增加了':'减少了').' '.abs($data['num']).' '.$_dw.'，原因：'.$data['about'].'，操作员：'.$this->user['username'];
	        send_msg($data['uid'],$title,$content);
	        send_wx_msg($data['uid'], $content);
	        $this->success('修改成功', 'index');
	    }
	    
	    $array = jf_name();
	    $array['rmb'] = '余额';
	    $array['rmb_freeze'] = '冻结余额';
	    $this->form_items = [
	            ['hidden', 'uid'],
	            ['static', 'username', '用户名','用户名不可修改'],
	            ['radio', 'type', '修改种类','',$array],
	            ['money', 'num', '变化数量','3代表增加3个,-3代表扣除3个,正数是增加,负数就扣除'],
	            ['text', 'about', '变更原因(可为空)',''],
	    ];
	    
	    //联动显示
// 	    $this->tab_ext['trigger'] = [
// 	            ['type', substr(implode(',',array_flip(jf_name())), 2), 'num'],
// 	    ];
	    return $this->editContent($info);
	}
	
	
	public function add(){
	}
	
	public function delete($ids = null){
	}
	
}
