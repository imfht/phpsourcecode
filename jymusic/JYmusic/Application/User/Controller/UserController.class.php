<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace User\Controller;
use Think\Controller;
use User\Api\UserApi;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class UserController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Home/Index/index');
	}
	/**
     *用户控制器初始化
     */
    protected function _initialize(){
        /* 读取数据库中的配置 */
        $config = api('Config/lists');
        C($config); //添加配置
       	define('UID',is_login());      	
       	//个人空间访问无需验证登录
       	if ('Home' == CONTROLLER_NAME){
	    	$uid = intval(I('get.uid')); 	
	    	if (!$uid){
	    		if (!UID){
	    			$this->error('非法访问');
	    		}else{
	    			$this->login_user = $user = M('Member')->find(UID);	
	    		}
	    	}else{
	    		if (!UID){
	    			$user = M('Member')->find($uid);//获取当前空间用户	
	    		}else{
	    			if (UID == $uid){
	    				$this->login_user = $user = M('Member')->find(UID);	
	    			}else{
	    				$user = M('Member')->find($uid);//获取当前空间用户    				
	    				$this->login_user = M('Member')->find(UID);	
	    			}	    			
	    		}    		
	    	}    	    		    	  
			if(is_array($user) && $user['status']){
				if(!$user['space']){
					if(C('USER_SPACE_OPEN')){//自动开通空间
						if($this->openspace($uid)){
							M('Member')-> where($data)->setField('space',1);
						}else{
							$this->error('个人空间暂未开通');
						}
					}else{//用户自己开通												
						$this->error('个人空间还没有开通或已关闭！',U('/'));
					}					
				}else{
					$userSpace = M('UserSpace')->where(array('uid'=>$uid))->find();//获取当前用户
					if(!empty($userSpace)){
						$user = array_merge ($userSpace,$user);
					}
		    		$this->user =  $user;
		    		$this->assign('user',$user);
		    	}		    	    		
	    	}else{
				$this->error('用户不存在或被禁用！');
			}
	    	
	    }else{
	       if(!UID){// 还没登录 跳转到登录页面
	        	//Cookie('__forward__',$_SERVER['REQUEST_URI']);
	            $this->error('您还没有登录，请先登录！', U('/Member/login'));
	        }else{
				$user = M('Member')->find(UID);//获取当前用户
				$userSpace = M('UserSpace')->where(array('uid'=>UID))->find();//获取当前用户
				if(!empty($userSpace)){
						$user = array_merge ($userSpace,$user);
				}
				$this->login_user = $user;//获取当前用户	
			}
	    }
        // 是否是超级管理员
        define('IS_ROOT', is_administrator());
        if(!IS_ROOT && C('ADMIN_ALLOW_IP')){
            // 检查IP地址访问
            if(C('WEB_SITE_CLOSE')){
	            if(!in_array(get_client_ip(),explode(',',C('HOME_ALLOW_IP')))){
	                $this->error('403:禁止访问');
	            }
	        }       
	        if(!IS_ROOT && !C('WEB_SITE_CLOSE')){
	            $this->error(C('WEB_OFF_MSG'));
	        }
	    }


		//dump(CONTROLLER_NAME);      
    	$this->meat_title = C('WEB_SITE_TITLE');
       	$this->meat_keywords = C('WEB_SITE_KEYWORD');
       	$this->meat_description = C('WEB_SITE_DESCRIPTION');
    }
   /**
     * 开通个人空间
     * @return array|false
     * 返回数据集
     */
 	protected function openspace ($uid){
 		$data['uid'] = $uid;
 		$user = M('UserSpace')->where($data)->find();
 		if (empty($user)){					
			$uname = get_nickname($uid);
			$data['uname'] = $uname;
			$data['title'] = $uname .'的展示页';
			$data['indexunit'] ='newShare,hotShare,newMessage';
			$data['sidebarunit'] ='signature,hotUser';
			$path = __ROOT__.trim(C('VIEW_PATH'),'.').'User';	
			$data['bg']= $path .'/space_skins/user_bg/default.jpg';
			$data['banner']=  $path .'/space_skins/user_banner/default.jpg';
			$id =  M('UserSpace')->add($data);
		}else{
			$id = true;
		}
		return $id;
 	}


	/**
     * 通用分页列表数据集获取方法
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(), $order='',$field=true, $status = 1){
    	
        if(is_string($model)){
        	$model = ucfirst($model);
        	//$config =F('web/Config');        	        	        	
        	$where['status']= $status;
        	if('Songs' == $model ){
        		$songsList=C('SONGS_LIST_ROWS');
            	$listRows = isset($songsList) ? $songsList : 20;
            	$field = !is_null($field)? $field:'description';            	 
        	}elseif('Album' == $model){
        		$albumList=C('ALBUM_LIST_ROWS');
            	$listRows = isset($albumList) ? $albumList : 15;
            	$field = !is_null($field)? $field:'company,description,sort,pub_time';
        	}elseif('Artist' == $model){
        		$singerList=C('SINGER_LIST_ROWS');
        		$listRows = isset($singerList) ? $singerList : 15;
        		$field = !is_null($field)? $field:'description,sort';
        	}else{
        		$listRows = 20;
        	}       	
            $model  =   M($model);
        }
        $order = !is_null($order)?$order:'id DESC';//设置排序
        $total        =   $model->where($where)->count();//获取总数
        $page = new \Think\Page($total, $listRows);
        $page->rollPage = 3;
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $page->setConfig('prev', '<');
        	$page->setConfig('next', '>');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $limit = $page->firstRow.','.$page->listRows;
        return $model->where($where)->field($field)->limit($limit)->order($order)->select();
    	
    }

}
