<?php
namespace Home\Controller;
use Think\Controller;
use Common\Api\CategoryApi;
use Org\Util\Tree;
class HomeController extends Controller{

	
/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}
    
    protected function _initialize(){
    	
      /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
                   
           	
        if(!C('WEB_SITE_CLOSE')&&$_SESSION[C('USER_AUTH_KEY')] != 1){
        	
        	
        	
        	
            $this->error('站点已经关闭，请稍后访问~','',1000);
            
        }
    	
        $a=  D('Member')->need_login();
       
        $field=array('uid','username','nickname','reg_time','space_url','last_login_time','avatar32', 'avatar64', 'avatar128', 'avatar256');
       
        $userinfo=query_user($field,$_SESSION['zs_home']['user_auth']['uid']);
        $roleauth=getmroleauth();
        checkscore($_SESSION['zs_home']['user_auth']['uid']);
   
    	
        $mymail['status']=1;	
	    $mymail['is_read']=0;
	    $mymail['to_uid']=$_SESSION['zs_home']['user_auth']['uid'];
    	$mymailcount=M('Message')->where($mymail)->count();
    	$userinfo['mymailcount']=empty($mymailcount)?'':$mymailcount;
        $isadmin=is_admin($_SESSION['zs_home']['user_auth']['uid']);
        
        $this->assign('isadmin',$isadmin);
        
        $this->assign('user_auth',session('user_auth'));
        $this->assign('uid',getnowUid());
         $this->assign('userinfo',$userinfo);
         
         $this->assign('roleauth',$roleauth);//得到会员组权限
         
         
        $cname=strtolower(CONTROLLER_NAME);
        $aname=strtolower(ACTION_NAME);
        
          $this->assign('aname',$aname);
          $this->assign('cname',$cname);
          
      
          $cate=new CategoryApi();
          $clist=$cate->get_catelist(0);
         
         
          $clistnum=$cate->get_editcnum();
           $this->assign('clist',$clist);
          
           $nosigncate=M('Cate')->where(array('status'=>1,'type'=>1))->select();
           $m = D('cate');
           $catelist = $m->field('*,CONCAT(spid,id) as path2')->where(array('type'=>1,'status'=>1))->order('path2')->select();
           $t = new tree();
           $catelistarr = $t->unlimitCategoryFormat($catelist);
           $catehtml=$t->treeFormat($catelistarr);
          $this->assign('catehtml',$catehtml); 
           $this->assign('nosigncate',$nosigncate);
           
           $this->assign('clistnum',$clistnum);

           $nav=D('nav')->where(array('status'=>1))->order('sort desc')->select();
           foreach($nav as $key =>$vo){
           	
           $nav[$key]['url']=navurl($vo['id'], $vo['type']);
          
           $nav[$key]['active']=navactive($vo['id'], $vo['type']);
          if($vo['win']){
          	$nav[$key]['target']='_blank';
          	
          }else{
          	$nav[$key]['target']='_self';
          	
          }
           }
         
           $this->assign('nav',$nav);
          
        
      
      
    }
   
	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测,可以设计为ajax验证 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}

    protected function ensureApiSuccess($result)
    {
        if (!$result['success']) {
            $this->error($result['message'], $result['redirect']);
        }else{
        	$this->success($result['message'],$result['redirect']);
        }
    }
   public function send_mail($to, $type){
   	
   	$uid=is_login();
   	
   
   	if($type==1){
   		//验证邮件
   		$map['id']=array('neq',$uid);
   		$map['email']=$to;
   		$r=M('ucenter_member')->where($map)->find();
   		
   		
   		if($r!=''){
   			
   		$this->error('该邮件地址已经被别人注册了！');	
   		}
   		
   		$name=get_username($uid);
   		 $subject=C('WEB_SITE').'邮箱验证邮件';
   		$body='请点击以下链接完成邮箱验证,有效时间为5分钟：<br />'."http://$_SERVER[HTTP_HOST]".U('Userbase/yzmail',array('uid'=>think_encrypt($uid,'',3000),'mail'=>think_encrypt($to,'',3000)));
       	
   	}
   	
   	if($type==2){
   		 $subject=C('WEB_SITE').'：恭喜您，注册成功！';
   		$body=C('MAIL_USER_REG');
   		$name=get_username($uid);
   	}

   	
   	
    $res=send_mail($to, $subject, $body, $name, $attachment);   
   	if($res==1){
   		$this->success('邮件已发送，请到邮箱进行查收');
   	}else{
   		$this->error('邮件发送失败，请检查邮箱设置');
   	}
   	
   
   }
// 检测输入的验证码是否正确，$code为用户输入的验证码字符串	  
	public function check_verify($code, $id = ''){
		$verify = new \Think\Verify();
		
		return $verify->check($code, $id);
	}	
	
	//生成  验证码 图片的方法
	public function verify() {             
        $config =    array(    
        'fontSize'    =>    30,   
        'length'      =>    4,    
        
        'useCurve'    =>    false, 
        );
        $Verify = new \Think\Verify($config);
        
        //$Verify->codeSet = rand_string(4,9); 
      
        
        $Verify->entry();                      
    }	

}