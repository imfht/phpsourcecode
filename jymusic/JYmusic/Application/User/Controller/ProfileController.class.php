<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
use Think\Image;
class ProfileController extends UserController {
	/*
	*个人设置
	*/	
	function index() {
		$data =  M('Member')->where(array('uid'=>UID))->field('uid,nickname,sex,qq,signature,space')->find();
		$this->meat_title = '个人设置 - '.C('WEB_SITE_TITLE');
		$this->assign('user',$data);
		$this->display();   
	}
	
	/**
    * 修改密码提交
    */
    public function submitPassword(){
        //获取参数
        $password   =   I('post.old');
        $uid  =   UID;
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if($data['password'] !== $repassword){
            $this->error('您输入的新密码与确认密码不一致');
        }

        $Api    =   new UserApi();
        $res    =   $Api->updateInfo($uid, $password, $data);
        if($res['status']){
            $this->success('密码修改成功！');
        }else{
            $this->error($res['info']);
        }
    }
    
    /**
     * 修改资料提交
     */
    public function setConfig(){
		if(IS_POST){
	        $Member =   D('Member');
	        
			$this->checknickname(I('post.nickname'));
			$this->checkqq(I('post.qq'));
	        $data   =   $Member->create();
	        $uid = UID;
	        if($data){
	        	$res = $Member->where('uid='. $uid)->save();
		        if($res){
		            $user               =   session('user_auth');
		            $user['username']   =   $data['nickname'];
		            session('user_auth', $user);
		            session('user_auth_sign', data_auth_sign($user));
		            $this->success('修改成功！');
		        }else{
		            $this->error('修改失败！');
		        }
	            
	        }else{
	        	$this->error($Member->getError());
	        }        

    	}else{
    		$this->error('非法参数！');
    	}
    }
    
    /*
    *隐私设置
    */
    function setPrivacy () {
    
    
    }
    
    /*
    *消息设置
    */
    function setMsg () {
    
    
    }
      
    /*
    *同步设置
    */
    function sync () {
    
    
    } 
    
        
    /*
    *设置头像
    */
    
   	public function Avatars () {
    	/*************按照坐标裁切图片********************/   	
    	if(IS_POST){   		
			//$action = I('get.action');			 		
			$post = I('post.');
			$imgPath = '.'.$post['path'];
			$crop  = $post['crop'];
			$image = new \Think\Image();
    		$image->open($imgPath);  		
			$image->crop($crop['w'], $crop['h'],$crop['x'],$crop['y'])->save($imgPath);
			$image->thumb(256, 256,\Think\Image::IMAGE_THUMB_FIXED)->save($imgPath);			
			$imgPath1 = str_replace("256","128",$imgPath);
			$image->thumb(128, 128,\Think\Image::IMAGE_THUMB_FIXED)->save($imgPath1);
			$imgPath2 = str_replace("256","64",$imgPath);
			$image->thumb(64, 64,\Think\Image::IMAGE_THUMB_FIXED)->save($imgPath2);
			$id = M('Member')-> where(array('uid'=>UID))->setField('pic_id',$post['id']);
			if($id){
				$this->success('头像修改成功！',U('Profile/index'));
			}else{
				$this->success('头像保存失败！');
			}
		}else{
			$this->meat_title = '设置头像 - '.C('WEB_SITE_TITLE');
			$this->assign('type','portrait');
			$this->display('index');
		}
    
    }
    
    
	private function checknickname ($nickname) { 	
   			$str  = @explode(',',trim(C('REG_BAN_NAME')));
			if(count($str)>0){
				for( $i=0;$i<count($str);$i++){
	 				if( stristr($nickname,$str[$i])){
	 					$this->error('昵称中含有非法字符');
	 				}
				}
			}
			$name = D('Member')->getFieldByUid(UID,'nickname');
			if( $name  != $nickname){
				$count = D('Member')->where(array('nickname'=> $nickname))->count();
				if($count){
					$this->error('昵称已存在');
				}
			}
	
	}
	
	private function checkqq ($qq) { 	
			$qq1= D('Member')->getFieldByUid(UID,'qq');
			if( $qq1 != $qq){
				$count = D('Member')->where(array('qq'=> $qq))->count();
				if($count){
					$this->error('qq号码已存在');
				}
			}
	
	}
}