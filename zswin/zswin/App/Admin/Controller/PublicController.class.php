<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util\Rbac;
use User\Api\UserApi;

class PublicController extends Controller {
	
public function mtReturn($status,$info,$navTabId='',$callbackType='closeCurrent',$forwardUrl='',$rel='', $type='') {
		// 保证AJAX返回后也能保存日志
		 
	   
		
		 	
		 	
		$result = array();
		

		if($navTabId==''){
			$navTabId=$_REQUEST['navTabId'];
		}
		if($status=='200'){
			$this->sysLogs('', $info);
		}
		if($status=='201'){
			$status=200;
		}


		$result['statusCode'] = $status; // dwzjs
		$result['navTabId'] = $navTabId; // dwzjs
		$result['callbackType'] = $callbackType; // dwzjs
		$result['message'] = $info; // dwzjs
		$result['forwardUrl'] = $forwardUrl;
		$result['rel'] = $rel;
			
		if (empty($type))
		$type = C('DEFAULT_AJAX_RETURN');
		if (strtoupper($type) == 'JSON') {
			// 返回JSON数据格式到客户端 包含状态信息
			header("Content-Type:text/html; charset=utf-8");
			exit(json_encode($result));
		} elseif (strtoupper($type) == 'XML') {
			// 返回xml格式数据
			header("Content-Type:text/xml; charset=utf-8");
			exit(xml_encode($result));
		} elseif (strtoupper($type) == 'EVAL') {
			// 返回可执行的js脚本
			header("Content-Type:text/html; charset=utf-8");
			exit($data);
		} else {
			// TODO 增加其它格式
		}
	}
   public function sysLogs($opname='未知', $message='未知') {
		$syslogs = D("Syslogs");
		$data = array();
		$ip = get_client_ip();
		$data['modulename'] = '公共模块';
		$data['actionname'] = getactionname();
		$data['opname'] = $opname;
		$data['message'] = $message;
		$data['username'] = $_SESSION['zs_admin']['user_auth']['username'];
		$data['userid'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['userip'] = $ip;
		$data['create_time'] = time();
		$result = $syslogs->add($data);
	}
	
    public function cleancache(){


    	
		//清文件缓存
		$dirs	=	array('./Runtime/');
		//清理缓存
		foreach($dirs as $value) {
			if(rmdirr($value)){
				$data.='文件夹'.$value.'删除成功;</br>';
				@mkdir($value,0777,true);
			}

		}
		
		$this->mtReturn(200,$data);
		
		
	}
  public function login($username = null, $password = null, $verify = null){
  	
  	    $verifyconfig=M('config')->where(array('name'=>'VERIFY_OPEN'))->getField('value');
  	    $verifyarr=explode(',', $verifyconfig);
        if(IS_POST){
            /* 检测验证码 TODO: */
        	 if(in_array('3', $verifyarr)){
           if(!$this->check_verify($verify)){
            $this->error('验证码输入错误！');
           } 
        	 }
            /* 调用UC登录接口登录 */
            $User = new UserApi;
            $uid = $User->login($username, $password);
            if(0 < $uid){ //UC登录成功
            	
            	
                /* 登录用户 */
                $Member = D('Member');
                if($Member->login($uid)){ //登录用户
                    //TODO:跳转到登录前页面
                    
                      $_SESSION[C('USER_AUTH_KEY')] = $uid;
                   $this->sysLogs("用户登录", '成功');
                    $this->success('登录成功！', U('Index/index'));
                } else {
                    $this->error($Member->getError());
                }

            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
            	if(in_array('3', $verifyarr)){
            		$this->assign('isverify',1);
            	}else{
            		
            		$this->assign('isverify',0);
            	}
               $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            D('Member')->logout();
            session('[destroy]');
            $this->sysLogs("用户退出", '成功');
            
            
            $data['statusCode']=200;
            $data['message']='退出成功！';
            $this->ajaxReturn($data);
           // $this->success('退出成功！', U('login'));
        } else {
        	 session('[destroy]');
            $this->redirect('login');
        }
    }


// 更换密码
	public function changePwd() {
		
		
	 //获取参数
            $uid = is_login();
            $password = I('post.oldpassword');
            $newpassword = I('post.password');
            $repassword = I('post.repassword');
            
            $data['password'] = $newpassword;
            empty($password) && $this->mtReturn(300,'请输入原密码');
            empty($newpassword) && $this->mtReturn(300,'请输入新密码');
            empty($repassword) && $this->mtReturn(300,'请输入确认密码');

            if ($data['password'] !== $repassword) {
            	
			    $this->mtReturn(300,'您输入的新密码与确认密码不一致');
			    
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if ($res['status']) {
               // $this->success('修改密码成功！');
               
			    $this->mtReturn(200,'密码修改成功！');
			  
            } else {
            	
			    $this->mtReturn(300,$res['info']);
			    
               
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

?>