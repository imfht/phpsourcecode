<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Home\Controller;
use Think\Controller;
/**
 * 前台音乐数据处理
 */
class DownController extends HomeController {
    
	public function index() {
    	$id=I("id");	//用户提交的id    	
    	$data = M('songs')->where(array('id'=>$id))->find();
    	if(!empty($data)){
    		$user = M('Member')->where(array('uid'=>$data['up_uid']))->field('status,cdkey',true)->find();
    		$title = '下载'.$data['name'];
			$this->meat_title = $title.' - '.C('WEB_SITE_NAME');
			$this->title = $title;
			$this->assign('user',$user);
    		$this->assign('data',$data);

  			$this->display();
    	}else{
    		$this->error('音乐不存在！');
    	}    	
    }
    
    
    /*下载检测*/   
    public function check () {    	    	   	  	
    	$id=I("post.id");	//用户提交的id
    	if (IS_AJAX && $id) {
    		// 获取当前用户ID     
	       	if(!$uid = is_login()){// 还没登录 
	       		$data['info'] = '请登录后在操作！';       		
	       		$data['status']  = 2;	//状态码 2 为没有登录
	       		$data['url']    =   U('Member/login'); // 成功或者错误的跳转地址 		               	
			    $this->ajaxReturn($data);
	        }else{ 
	        	//检测歌曲所需积分
	        	$Songs = M('Songs');
	        	$Member = M('Member');
	    		$map['id'] = $id;
	    		$map['status'] = 1;
	    		$list=$Songs->field('name,music_down,gold')->where($map)->find();
	    		extract($list);	    		
	    		$gold = intval($gold);
	    		$userScore = $Member->getFieldByUid($uid ,'score');//获取该用户积分
	    		$userScore= intval($userScore); //转为数字	
	    		$data['status']  = 1;
	    		if($gold){//收费歌曲
	    			//24小时内下载不扣积分
	    			$map['uid'] = $uid ;
	    			$map['music_id'] = $id;
	    			$udata = M("UserMusic")->where($map)->field('create_time')->order('create_time desc')->find();
		    		if (date("Y-m-d ", time()) != date("Y-m-d ",$udata['create_time'])){
		    			if ( $userScore >= $gold ){//检测积分
		    				$data['gold'] =  $gold;
							$data['info'] = '下载将扣除'.$gold.'积分';	
							$Member->where(array('uid'=>$uid))->setDec('score',$gold);    				
		    			}else{
		    				$this->error('积分不足无法下载,你当前的积分为:'.$userScore);	    				
		    			}		    			
		    		}    				    		
	    		} 	    		
	    		//记录下载信息 
	    		$up['uid'] = $uid ;
				$up['uname'] = get_nickname($uid); 
				$up['music_id'] = $id; 
				$up['music_name'] = $name;
				$up['user_ip'] = get_client_ip();
				$up['status'] = 1;
				$up['create_time'] = NOW_TIME;
				M("UserDown")->add($up);//添加下载记录 
				$Songs->where($map)->setInc('download'); // 下载数加1				
	    		$data['url'] = U('Down/localDown',array('id'=>$id,));
	    		if (filter_var ($music_down, FILTER_VALIDATE_URL)){//远程文件
	    			$data['thunderurl'] = $music_down;
	    		}else{
	    			$music_down = 'http://'.$_SERVER["HTTP_HOST"].$music_down;
	    			$data['thunderurl'] ="thunder://".base64_encode("AA".$music_down."ZZ");
					
	    		}
				$this->ajaxReturn($data);
	   		}
   		}else{
   			$this->error('出错啦！');		
   		}
    }  
    
    public function localDown (){ 
    	header("Content-type:text/html;charset=utf-8");  	
    	if(!$uid = is_login()){//双重验证防止非法获取
    		$this->error('请登录后在操作');
    	}else{
	    	$map['id'] = I("id");
	    	$list=M('Songs')->field('name,artist_id,artist_name,music_down')->where($map)->find();
	    	extract($list);		    
		    if (filter_var ($music_down, FILTER_VALIDATE_URL)){//远程文件
				$telefile = get_headers($music_down,1);	
				$location = $telefile['location'];			
				if (!empty($location)){//远程解析地址
					$length = $telefile['Content-Length'][1];
					$info = pathinfo($telefile['Content-Disposition']);
					
				}else{//绝对远程地址								
					$length = $telefile['Content-Length'];
					$info = pathinfo($music_down);	
				}
				$ext = '.'.trim($info ['extension'],'"');
			}else{
				$filedir = '.'.str_replace(__ROOT__,'',$music_down);
				if (!file_exists($filedir )) { //检测文件是否存在
					$this->error('对不起文件不存在！');
				}else {
					$length = filesize($filedir);
					$info = pathinfo($filedir );
					$music_down= 'http://'.$_SERVER["HTTP_HOST"].$music_down;
					$ext = '.'.$info ['extension'];					
				}			
			}
			if (!$artist_id){
				$name =  $name.$ext;
			}else{
				$name =  $artist_name.'-'.$name.$ext;
			}
			header('Content-Description: File Transfer'); 
			header('Content-Type: application/octet-stream'); 
			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
				header('Content-Disposition: attachment; filename='.rawurlencode($name));
			} else {			
				header('Content-Disposition: attachment; filename='.$name);
			} 
			header('Content-Transfer-Encoding: binary'); 
			header('Expires: 0'); 
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
			header('Pragma: public'); 
			header('Content-Length: '.$length); 
    	    readfile($music_down); 
    		exit;

		}   
	}    
    
}