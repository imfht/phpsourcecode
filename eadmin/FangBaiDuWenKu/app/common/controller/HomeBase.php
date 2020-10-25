<?php
namespace app\common\controller;
use think\Cache;
use app\common\logic\Nav as LogicNav;
use app\common\logic\User as LogicMember;
use app\common\logic\Common as LogicCommon;

class HomeBase extends ControllerBase
{
	private static $navLogic = null;
	private static $memberLogic = null;
	public static $commonLogic = null;
	
    protected function _initialize()
    {
        parent::_initialize();
       self::$navLogic = get_sington_object('navLogic', LogicNav::class);
       self::$memberLogic = get_sington_object('memberLogic', LogicMember::class);
       self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class);
       
       $alldocviewcount=model('doccon')->sum('view');
       $this->assign('alldocviewcount',$alldocviewcount);
       $alldocdowncount=model('doccon')->sum('down');
       $this->assign('alldocdowncount',$alldocdowncount);
       
       
       $this->assign('actionname',strtolower(CONTROLLER_NAME.'/'.ACTION_NAME));
       
       $uid=is_login();
       if($uid>0){
       	
       $nowusermess = 	self::$commonLogic->getDataList('readtime',['uid'=>$uid],'create_time','create_time desc',false,'','',1);
       $midarr=model('readmessage')->where(['uid'=>$uid])->column('mid');
       if(!empty($nowusermess)){
       	$messcount=model('message')->where(['touid'=>$uid,'create_time'=>array('gt',$nowusermess[0]['create_time']),'id'=>array('not in',$midarr)])->count();
       }else{
       	$messcount=model('message')->where(['touid'=>$uid,'id'=>array('not in',$midarr)])->count();
       }
       
       
       $this->assign('messcount',$messcount);
       }
       $this->assign('nowuid',$uid);
     
       $this->getSystem();//获得全站配置信息
       $this->getNav();//获取前台导航
       $this->autologin();
       //每天要定时的看悬赏任务是否到期
       empty($this->param['keyword']) ? $keyword = '' : $keyword = $this->param['keyword'];
       empty($this->param['ext']) ? $ext = 0 : $ext = $this->param['ext'];
       $this->assign('ext',$ext);
       $this->assign('keyword',$keyword);
       
       $pointarr=parse_config_attr(config('scoretype_list'));
       //获得升级积分
       $this->assign('gpointname',$pointarr['expoint1']);
       //获得下载上传的积分名称
       
       $this->assign('pointname',$pointarr['point']);
       
       
    }
    public function autologin(){
    	
    	if(!is_login()){
    		
    		$user = unserialize(decrypt(cookie('sys_key')));
    		if ((empty($user['userinfo']))){
    			 
    		}else {
    			
    			self::$commonLogic->setDataValue('user',['id' => $user['userinfo']['id']], 'last_login_time', TIME_NOW);
    			//self::$memberLogic->setMemberValue(['id' => $user['userinfo']['id']], 'last_login_time', TIME_NOW);
    			 
    			$auth = ['member_id' => $user['userinfo']['id'], 'last_login_time' => TIME_NOW];
    			$cook=array('id'=>$user['userinfo']['id'], 'userinfo'=>$user['userinfo'],'auth'=>$auth);
    			systemSetKey($cook);
    			 
    			
    			session('member_info', $user['userinfo']);
    			session('member_auth', $auth);
    			session('member_auth_sign', data_auth_sign($auth));
    			 
    			 
    		}
    		
    		
    	}
    	
    	
    }
  
    /**
     * 获取站点信息
     */
    public function getSystem()
    {

    }

    /**
     * 获取前端导航列表
     */
    public function getNav()
    {
        if (Cache::has('nav')) {
            $nav = Cache::get('nav');
        } else {
            
            $nav = self::$commonLogic->getDataList('nav',['status' => 1], true, 'sort asc',false);
            if (!empty($nav)) {
                Cache::set('nav', $nav);
            }
        }

        $this->assign('nav', $nav);
       
    }


}