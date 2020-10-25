<?php
namespace User\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index($id=false,$page=1){
    	if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($id)) {
	    	if(mc_user_id()!=$id) {
		    	$args_id = M('meta')->where("meta_key='author' AND meta_value='$id'")->getField('page_id',true);
		    	if($args_id) {
		        	$condition['id']  = array('in',$args_id);
					$this->page = M('page')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
			    	$count = M('page')->where($condition)->count();
		    	};
		        $this->assign('id',$id);
		        $this->assign('count',$count);
		        $this->assign('page_now',$page);
		    	$this->theme(mc_option('theme'))->display('User/pages');
		    } else {
			    //我关注的
			    $args_id1 = M('action')->where("action_key='perform' AND action_value='guanzhu' AND user_id = '$id'")->getField('page_id',true);
			    //@我的
			    $args_id2 = M('action')->where("action_key='at' AND page_id = '$id'")->getField('page_id',true);
			    //我的文章发生的
			    $args_id3 = M('meta')->where("meta_key='author' AND meta_value='$id'")->getField('page_id',true);
			    //最终
			    $my_id = array($id);
			    if($args_id1) {
				    if($args_id2) {
				    	$args_id = array_merge($args_id1,$args_id2,$my_id);
				    } else {
					    $args_id = array_merge($args_id1,$my_id);
				    };
			    } else {
			    	if($args_id2) {
			    		$args_id = array_merge($args_id2,$my_id);
			    	} else {
				    	$args_id = $my_id;
			    	}
			    };
			    if($args_id2 && $args_id3) {
				    $args_id5 = array_merge($args_id3,$args_id2);
			    } elseif($args_id2) {
			    	$args_id5 = $args_id2;
			    } elseif($args_id3) {
			    	$args_id5 = $args_id3;
			    };
			    $condition1['user_id']  = array('in',$args_id);
			    $condition1['page_id'] = array('in',$args_id5);
			    $condition1['_logic'] = 'OR';
			    $args_id4 = array('at','perform','comment','comment2','wish');
			    $condition['action_key'] = array('in',$args_id4);
			    $condition['_complex'] = $condition1;
				$this->page = M('action')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
		    	$count = M('action')->where($condition)->count();
		        $this->assign('id',$id);
		        $this->assign('count',$count);
		        $this->assign('page_now',$page);
		        $this->theme(mc_option('theme'))->display('User/index');
			    do_go('user_home_end');
		    }
		} else {
	     	$this->error('请登陆！',U('User/login/index'));
	    };	}
	public function pages($id=false,$page=1){
		if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($id)) {
		    $args_id = M('meta')->where("meta_key='author' AND meta_value='$id'")->getField('page_id',true);
		    if($args_id) {
		        $condition['id']  = array('in',$args_id);
				$this->page = M('page')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
			    $count = M('page')->where($condition)->count();
		    };
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('User/pages');
		} else {
	     	$this->error('参数错误！',U('User/login/index'));
	    };	}
	public function comments($id=false,$page=1){
		if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($id)) {
		    $this->page = M('action')->where("action_key='comment' AND user_id='$id'")->order('id desc')->page($page,mc_option('page_size'))->select();
		    $count = M('action')->where("action_key='comment' AND user_id='$id'")->count();
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('User/comments');
		} else {
	     	$this->error('参数错误！',U('User/login/index'));
	    };	}
    public function edit($id=false){
    	if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($id)) {
	    	if(mc_user_id()==$id) {
		        if(mc_remove_html($_POST['title'],'all')) {
			        $title = M('page')->where("title='".mc_magic_in(mc_remove_html($_POST['title'],'all'))."' AND type ='user'")->getField('id');
		        	if(is_numeric($title) && $title!=$id) {
			        	$this->error('昵称已存在！');
		        	} else {
			        	mc_update_page(mc_user_id(),mc_magic_in(mc_remove_html($_POST['title'],'all')),'title');
		        	};
			        if($_POST['content']) {
				        mc_update_page(mc_user_id(),mc_magic_in(mc_remove_html($_POST['content'],'all')),'content');
			        };
			        if($_POST['user_avatar']) {
			        	if(mc_get_meta(mc_user_id(),'user_avatar',true,'user')) {
			        		mc_update_meta(mc_user_id(),'user_avatar',mc_magic_in(mc_save_img_base64($_POST['user_avatar'],1,120,120)),'user');
			        	} else {
				        	mc_add_meta(mc_user_id(),'user_avatar',mc_magic_in(mc_save_img_base64($_POST['user_avatar'],1,120,120)),'user');
			        	}
					};
					if($_POST['fmimg']) {
						mc_delete_meta($id,'fmimg','basic');
						mc_add_meta($id,'fmimg',mc_magic_in(mc_save_img_base64($_POST['fmimg'])));
					};
					mc_delete_meta($id,'buyer_name','user');
					if($_POST['buyer_name']) {
				        mc_add_meta($id,'buyer_name',mc_magic_in($_POST['buyer_name']),'user');
			        };
			        mc_delete_meta($id,'buyer_province','user');
					if($_POST['buyer_province']) {
				        mc_add_meta($id,'buyer_province',mc_magic_in($_POST['buyer_province']),'user');
			        };
			        mc_delete_meta($id,'buyer_city','user');
					if($_POST['buyer_city']) {
				        mc_add_meta($id,'buyer_city',mc_magic_in($_POST['buyer_city']),'user');
			        };
			        mc_delete_meta($id,'buyer_address','user');
					if($_POST['buyer_address']) {
				        mc_add_meta($id,'buyer_address',mc_magic_in($_POST['buyer_address']),'user');
			        };
			        mc_delete_meta($id,'buyer_phone','user');
					if($_POST['buyer_phone']) {
				        mc_add_meta($id,'buyer_phone',mc_magic_in($_POST['buyer_phone']),'user');
			        };
			        $user_email_now = mc_get_meta(mc_user_id(),'user_email',true,'user');
			        if($_POST['user_email']!=$user_email_now) {
			        	$user_email = M('meta')->where("meta_key='user_email' AND type ='user'")->getField('meta_value',true);
			        	if(in_array(strip_tags($_POST['user_email']), $user_email)) {
				        	$this->error('邮箱已存在！');
			        	} else {
			        		mc_update_meta(mc_user_id(),'user_email',mc_magic_in($_POST['user_email']),'user');
			        	}
					} elseif($_POST['user_email']=='') {
						$this->error('邮箱必须填写！');
					} else {
						mc_update_meta(mc_user_id(),'user_email',mc_magic_in($_POST['user_email']),'user');
					};
					if(I('param.pass')) {
						if(I('param.pass2')==I('param.pass')) {
							mc_update_meta(mc_user_id(),'user_pass',md5(I('param.pass').mc_option('site_key')),'user');
							$this->success('修改密码成功，请使用新密码登陆',U('User/login/index'));
						} else {
							$this->error('两次密码必须填写一致！');
						}
					} else {
						$this->success('更新资料成功',U('User/index/edit?id='.$id));
					};
		        } else {
			        $this->theme(mc_option('theme'))->display('User/edit');
		        };
		     } else {
			     $this->error('禁止访问！');
		     }
	     } else {
	     	$this->error('参数错误！',U('User/login/index'));
	     };
    }
    public function site_control(){
    	if(mc_is_admin()) {
    		if($_POST['the_control']) {
		    	mc_update_option('home_title',I('param.home_title'));
		    	mc_update_option('home_keywords',I('param.home_keywords'));
		    	mc_update_option('home_description',I('param.home_description'));
		    	//商品
		    	mc_update_option('pro_title',I('param.pro_title'));
		    	mc_update_option('pro_keywords',I('param.pro_keywords'));
		    	mc_update_option('pro_description',I('param.pro_description'));
		    	//社区
		    	mc_update_option('group_title',I('param.group_title'));
		    	mc_update_option('group_keywords',I('param.group_keywords'));
		    	mc_update_option('group_description',I('param.group_description'));
		    	mc_update_option('paixu',I('param.paixu'));
		    	mc_update_option('shenhe_post',I('param.shenhe_post'));
		    	//文章
		    	mc_update_option('article_title',I('param.article_title'));
		    	mc_update_option('article_keywords',I('param.article_keywords'));
		    	mc_update_option('article_description',I('param.article_description'));
		    	$this->success('保存成功');
	    	} else {
		    	$this->error('提交参数错误！');
	    	}
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function site_nav(){
    	if(mc_is_admin()) {
    		if($_POST['nav_control']) {
	    		if($_POST['nav_blank']>0) {
		    		mc_add_option(I('param.nav_text'),I('param.nav_link'),'nav2');
	    		} else {
		    		mc_add_option(I('param.nav_text'),I('param.nav_link'),'nav');
	    		};
		    	$this->success('保存成功');
	    	} else {
		    	$this->error('提交参数错误！');
	    	}
    	} else {
	    	$this->success('请先登陆');
	    };
    }
    public function site_nav2(){
    	if(mc_is_admin()) {
    		if($_POST['nav_control']) {
	    		if($_POST['nav_blank']>0) {
		    		mc_add_option(I('param.nav_text'),I('param.nav_link'),'nav4');
	    		} else {
		    		mc_add_option(I('param.nav_text'),I('param.nav_link'),'nav3');
	    		};
		    	$this->success('保存成功');
	    	} else {
		    	$this->error('提交参数错误！');
	    	}
    	} else {
	    	$this->success('请先登陆');
	    };
    }
    public function shoucang($id=false){
    	if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
        if(is_numeric($id)) {
	        $args_id = M('action')->where("user_id='$id' AND action_key='perform' AND action_value='shoucang'")->getField('page_id',true);
	        if($args_id) {
	        	$condition['id']  = array('in',$args_id);
	        	if($_GET['type']) {
		        	$condition['type'] = mc_magic_in($_GET['type']);
	        	};
	        	$this->page = M('page')->where($condition)->order('id desc')->select();
        	};
	        $this->theme(mc_option('theme'))->display('User/shoucang');
	    } else {
	     	$this->error('参数错误！');$this->error('参数错误！');
	    };
    }
    public function pro($id=false,$page=1){
    	if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($id)) {
	    	if(mc_user_id()==$id) {
		    	$this->page = M('action')->where("user_id='".mc_user_id()."' AND action_key IN ('trade_wait_send','trade_wait_cofirm','trade_wait_finished','trade_wait_hdfk')")->order('id desc')->page($page,mc_option('page_size'))->select();
		    	$count = M('action')->where("user_id='".mc_user_id()."' AND action_key IN ('trade_wait_send','trade_wait_cofirm','trade_wait_finished','trade_wait_hdfk')")->count();
			    $this->assign('id',$id);
			    $this->assign('count',$count);
			    $this->assign('page_now',$page);
		    	$this->theme(mc_option('theme'))->display('User/pro');
	    	} else {
		    	$this->error('请不要偷窥别人的购买记录哦～');
	    	}
	    } else {
	     	$this->error('参数错误！');
	    };
    }
    public function coins($id=false,$page=1){
    	if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($id) && is_numeric($page)) {
	    	$condition['user_id'] = $id;
	    	$condition['action_key'] = 'coins';
	    	$this->page = M('action')->where($condition)->order('date desc')->page($page,mc_option('page_size'))->select();
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('User/coins');
		} else {
			$this->error('参数错误！');
		}
	}
    public function guanzhu($id=false,$page=1){
    	if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($page)) {
	    	$condition['type'] = 'user';
        	$args_id = M('action')->where("action_key='perform' AND action_value='guanzhu' AND user_id='$id'")->getField('page_id',true);
        	if($args_id) {
	        	$condition['id']  = array('in',$args_id);
		    	$this->page = M('page')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
			    $count = M('page')->where($condition)->count();
		    };
			$this->assign('count',$count);
			$this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('User/guanzhu');
		} else {
			$this->error('参数错误！');
		}
    }
    public function fans($id=false,$page=1){
    	if(!is_numeric($id)) {
    		$id = mc_user_id();
    	};
    	if(is_numeric($page)) {
	    	$condition['type'] = 'user';
        	$args_id = M('action')->where("action_key='perform' AND action_value='guanzhu' AND page_id='$id'")->getField('user_id',true);
        	if($args_id) {
	        	$condition['id']  = array('in',$args_id);
		    	$this->page = M('page')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
			    $count = M('page')->where($condition)->count();
		    };
			$this->assign('count',$count);
			$this->assign('page_now',$page);
			$this->theme(mc_option('theme'))->display('User/fans');
		} else {
			$this->error('参数错误！');
		}
    }
    public function tixian(){
    	if($_POST['shoukuan']=='') {
	    	$this->error('收款方式必须填写！');
    	} elseif($_POST['tixian']<100) {
	    	$this->error('提现金额必须大于100元！');
	    } elseif($_POST['tixian']>mc_coins(mc_user_id())) {
	    	$this->error('您没有足够的余额！');
    	} else {
	    	$coins = -$_POST['tixian'];
	    	mc_update_coins(mc_user_id(),$coins);
	    	$action['page_id'] = mc_user_id();
			$action['user_id'] = mc_user_id();
			$action['date'] = strtotime("now");
			//积分记录
			$action['action_key'] = 'coins';
			$action['action_value'] = $coins;
			M('action')->data($action)->add();
			//收款方式
			$action['action_key'] = 'shoukuan';
			$action['action_value'] = I('param.shoukuan');
			M('action')->data($action)->add();
			//收款状态
			$action['action_key'] = 'zhuangtai';
			$action['action_value'] = 1;
			M('action')->data($action)->add();
	    	$this->success('提现成功！');
    	}
    }
}