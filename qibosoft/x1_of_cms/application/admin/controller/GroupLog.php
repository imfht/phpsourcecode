<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Grouplog AS GrouplogModel;

use app\common\traits\AddEditList;

class GroupLog extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext;

	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new GrouplogModel();
	}
    
	public function pass($id,$status=0){
	    $info = GrouplogModel::get($id);
	    if (empty($info)) {
	        $this->error('资料不存在');
	    }
	    
	    $array = [
	            'id'=>$id,
	            'status'=>$status,
	            'check_time'=>time(),
	    ];
	    
	    if ($status==-1) {
	        if ($this->request->isPost()) {
	            $data = $this->request->post();
	            $array['refuse_reason'] = $data['refuse_reason'];
	        }else{
	            $this->tab_ext['page_title'] = '拒绝原因';
	            $this->form_items = [
	                ['textarea','refuse_reason','拒绝原因'],
	            ];
	            return $this -> addContent();
	        }
	    }
	    
	    $result = GrouplogModel::update($array);
	    if ($result) {
	        $gdb = getGroupByid($info['gid'],false);
	        
	        if (count($gdb['_level'])<=1) {
	            $day = $gdb['daytime'];
	        }else{
	            $day = $info['daytime'];
	        }
	        
	        $data = [
	            'uid'=>$info['uid'],
	            'groupid'=>$info['gid'],
	            'group_endtime'=>$day?($day*3600*24+time()):0,
	        ];
	        if ($status==1) {
	            $data['old_groupid'] = get_user($info['uid'])['groupid'];      //记录之前的用户组ID,方便到期后,恢复
	            edit_user($data);
	            $content = "你申请认证的身份为:“".getGroupByid($info['gid'])."”被通过审核了";
	            send_msg($info['uid'],"恭喜你，你申请的认证信息通过审核了",$content);
	            send_wx_msg($info['uid'], $content);
	            $this->success('审核成功');
	        }elseif($status==-1){
	            $content ="你申请认证的身份为:“".getGroupByid($info['gid'])."”被拒绝了，原因如下：<br>".$array['refuse_reason'];
	            send_msg($info['uid'], "很抱歉，你申请的认证信息被拒绝了",$content);
	            send_wx_msg($info['uid'], $content);
	            $this->success('拒绝成功','index');
	        }else{
	            $data['groupid'] = 8;      //取消审核的话,就会把用户级别降为普通会员
	            $data['group_endtime'] = 0;
	            edit_user($data);
	            $content ="你申请认证的身份为:“".getGroupByid($info['gid'])."”被取消审核了";
	            send_msg($info['uid'], "很抱歉，你申请的认证信息被取消审核了",$content);
	            send_wx_msg($info['uid'], $content);
	            $this->success('取消审核成功');
	        }	        
	    }else{
	        $this->error('更新失败!');
	    }
	}
	
	public function delete($ids = null) {
	    $ids = is_array($ids)?$ids:[$ids];
	    foreach ($ids AS $id){
	        $info = $this->model->get($id);
	        if ($info['status']==1) {
	            $this -> error('已通过审核的，不能直接删除，请先取消审核再删除');
	        }
// 	        $title = '很抱歉，你申请升级的用户组'.getGroupByid($info['gid']).'被拒绝了！';	        
// 	        $content = $title;
// 	        send_msg($info['uid'],$title,$content);
// 	        send_wx_msg($info['uid'], $content);
	    }
	    if ($this -> deleteContent($ids)) {
	        $this -> success('删除成功');
	    } else {
	        $this -> error('删除失败');
	    }
	}
    
	
	public function index($group=0)
    {
		$this->tab_ext = [
				'page_title'=>'用户认证升级管理',
		];

		$this->list_items = [
				['uid', '用户名', 'username'],              
				['gid', '申请用户组', 'callback',function($value){
				    return getGroupByid($value);
				}],
				['create_time', '申请日期', 'datetime'],
		        ['status', '审核与否', 'select2',[0=>'待审',1=>'<font color="green">已通过</font>',-1=>'<font color="blue">已拒绝</font>']],		
		        ['check_time', '审核日期', 'datetime'],
		        ['id', '审核操作', 'callback',function($value,$rs){
		            if($rs['status']==1){
		                $gdb = getGroupByid($rs['gid'],false);
		                $code = '<a href="'.urls('pass',['id'=>$value,'status'=>0]).'" title="点击取消审核"><i class="fa fa-ban"></i></a>';
		                $code.=$gdb['daytime']?' 有效期至：'.date('y-m-d H:i',$rs['check_time']+$gdb['daytime']*3600*24):'长期有效';
		            }elseif($rs['status']==-1){
		                $code = '<a href="'.urls('pass',['id'=>$value,'status'=>1]).'" title="点击通过审核"><i class="fa fa-check"></i></a>';
		            }else{
		                $code = fun('link@more',"<i class='fa fa-gears'></i>",[
		                    '查看资料'=>[
		                        'url'=>murl('member/user/index',['uid'=>$rs['uid']]).'?password='.mymd5($rs['uid']."\t".time()).'&gid='.$rs['gid'],
		                        'target'=>'_blank',
		                    ],
		                    '通过审核'=>urls('pass',['id'=>$value,'status'=>1]),
		                    '拒绝通过'=>urls('pass',['id'=>$value,'status'=>-1]),
		                ]);
		            }
		            
		            return $code;
		        }],
		];
		
		$this->tab_ext['top_button'] =[
		        [
		                'title' => '批量删除',
		                'icon'  => 'fa fa-times-circle',
		                'type'  => 'delete'
		        ],
		];
		$this->tab_ext['right_button'] =[
		    [
		        'title' => '删除',
		        'icon'  => 'fa fa-times-circle',
		        'type'  => 'delete'
		    ],
		];
		
		$map = [];
		$data = $this->model->where($map)->order('id','desc')->paginate(50);		
		return $this->getAdminTable( $data );
    }

}
