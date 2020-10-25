<?php
namespace Home\Controller;
use Think\Controller;
class PerformController extends Controller {
    public function xihuan($add_xihuan){
        if(is_numeric($_GET['add_xihuan'])) {
        $add_xihuan = $_GET['add_xihuan'];
        $user_xihuan = M('action')->where("page_id='$add_xihuan' AND user_id ='".mc_user_id()."' AND action_key='perform' AND action_value ='xihuan'")->getField('id');
        if(empty($user_xihuan)) {
        	mc_add_action($add_xihuan,'perform','xihuan');
        	$user_id = mc_author_id($add_xihuan);
        	do_go('add_xihuan_end',$user_id);
        };
        };
        $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
        //$date = strtotime("+8HOUR"); echo date('Y-m-d H:i:s',$date);
    }
    public function add_shoucang($add_shoucang){
    	if(is_numeric($_GET['add_shoucang'])) {
        $add_shoucang = $_GET['add_shoucang'];
        $user_shoucang = M('action')->where("page_id='$add_shoucang' AND user_id ='".mc_user_id()."' AND action_key='perform' AND action_value ='shoucang'")->getField('id');
        if(empty($user_shoucang)) {
        	mc_add_action($add_shoucang,'perform','shoucang');
        	$user_id = mc_author_id($add_shoucang);
        	do_go('add_shoucang_end',$user_id);
        };
        };
        $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
    }
    public function remove_shoucang($remove_shoucang){
    	if(is_numeric($_GET['remove_shoucang'])) {
        $remove_shoucang = $_GET['remove_shoucang'];
        M('action')->where("page_id='$remove_shoucang' AND user_id='".mc_user_id()."' AND action_key='perform' AND action_value = 'shoucang'")->delete();
        $user_id = mc_author_id($remove_shoucang);
        do_go('remove_shoucang_end',$user_id);
        };
        $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
    }
    public function add_guanzhu($add_guanzhu){
    	if(is_numeric($_GET['add_guanzhu'])) {
        $add_guanzhu = $_GET['add_guanzhu'];
        $user_guanzhu = M('action')->where("page_id='$add_guanzhu' AND user_id ='".mc_user_id()."' AND action_key='perform' AND action_value ='guanzhu'")->getField('id');
        if(empty($user_guanzhu)) {
        	mc_add_action($add_guanzhu,'perform','guanzhu');
			do_go('add_guanzhu_end',$add_guanzhu);
        };
        };
        $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
    }
    public function remove_guanzhu($remove_guanzhu){
    	if(is_numeric($_GET['remove_guanzhu'])) {
        $remove_guanzhu = $_GET['remove_guanzhu'];
        M('action')->where("page_id='$remove_guanzhu' AND user_id ='".mc_user_id()."' AND action_key='perform' AND action_value ='guanzhu'")->delete();
        };
        $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
    }
    public function comment($id,$content){
        if(mc_user_id()) {
	        $id = mc_magic_in($_POST['id']);
	        $content = mc_magic_in(mc_remove_html(mc_str_replace_base64($_POST['content']),'image'));
	        if($content) {
	        	$content = str_replace('@',' @',$content);
		        $content_array = explode(' ',$content);
				foreach($content_array as $val) :
				$content_s = strstr($val, '@');
				$to_user = substr($content_s, 1);
				if($to_user) :
				$idx = M('page')->where("title='$to_user'")->getField('id');
				$content_s2 .= '<a href="'.U('user/index/index?id='.$idx).'">'.$content_s.'</a> ';
				else :
				$content_s2 .= $val.' ';
				endif;
				endforeach;
				if($_POST['parent']>0) :
		        	$result = mc_add_action($_POST['parent'],'comment2',$content_s2);
		        else :
		        	$result = mc_add_action($id,'comment',$content_s2);
		        endif;
		        foreach($content_array as $val) :
				$content_s = strstr($val, '@');
				$to_user = substr($content_s, 1);
				if($to_user) :
				$idx = M('page')->where("title='$to_user'")->getField('id');
				mc_add_action($idx,'at',$result);
				do_go('publish_at_end',$idx);
				endif;
				endforeach;
				$user_id = mc_author_id($id);
				do_go('publish_comment_end',$user_id);
		        $type = mc_get_page_field($id,'type');
		        if($type=='publish') {
		        	if(mc_option('paixu')==2) {
		        		if(mc_get_page_field($id,'date')<=strtotime("now")) {
		        			mc_update_page($id,strtotime("now"),'date');
		        		}
		        	};
		        	mc_update_meta($id,'last_comment_user',mc_user_id());
			        $this->success('评论成功！',U('post/index/single?id='.$id.'#comment-'.$result));
		        } elseif($type=='article') {
			        $this->success('评论成功！',U('article/index/single?id='.$id.'#comment-'.$result));
		        } else {
			        $this->success('评论成功！',U('pro/index/single?id='.$id.'#comment-'.$result));
		        }
	        } else {
		        $this->error('请填写评论内容！');
	        }
	    } else {
		    $this->success('请先登陆',U('user/login/index'));
	    }
    }
    public function publish_img(){
	    echo mc_save_img_base64($_POST['src']);
	}
    public function publish(){
    	if(mc_user_id()) {
	    	if(mc_remove_html($_POST['title'],'all') && mc_remove_html($_POST['content'])) {
	    		$page['title'] = mc_magic_in(mc_remove_html($_POST['title'],'all'));
	    		$page['content'] = mc_magic_in(mc_remove_html(mc_str_replace_base64($_POST['content'])));
	    		if(mc_option('shenhe_post')==2) {
	    			if(mc_is_admin()) {
		    			$page['type'] = 'publish';
	    			} else {
		    			$page['type'] = 'pending';
	    			}
	    		} else {
		    		$page['type'] = 'publish';
	    		};
	    		$page['date'] = strtotime("now");
	    		$result = M('page')->data($page)->add();
		    	if($result) {
		    		mc_add_meta($result,'author',mc_user_id());
		    		if(is_numeric($_POST['group'])) {
			    		mc_add_meta($result,'group',$_POST['group']);
			    		mc_update_page(mc_magic_in($_POST['group']),strtotime("now"),'date');
			    		mc_add_meta($result,'time',strtotime("now"));
			    		if(is_numeric($_POST['number'])) {
				    		mc_add_meta($result,'number',$_POST['number']);
				    		mc_add_meta($result,'buyer_phone',$_POST['buyer_phone']);
				    		mc_add_meta($result,'buyer_address',$_POST['buyer_address']);
				    		mc_add_meta($result,'buyer_city',$_POST['buyer_city']);
				    		mc_add_meta($result,'buyer_province',$_POST['buyer_province']);
				    		mc_add_meta($result,'buyer_name',$_POST['buyer_name']);
				    		mc_add_meta($result,'wish',0);
				    		$parameter = $_POST['parameter'];
				    		if($parameter) :
								foreach($parameter as $key=>$valp) :
									mc_add_meta($result,'parameter',$key.'|'.$valp);
								endforeach;
							endif;
			    		}
		    		}
		    		do_go('publish_post_end',$result);
		    		if(mc_option('shenhe_post')==2) {
		    			if(mc_is_admin()) {
			    			$this->success('发布成功！',U('post/index/single?id='.$result));
		    			} else {
			    			$this->success('发布成功，请耐心等待审核',U('post/index/single?id='.$result));
		    			}
		    		} else {
			    		$this->success('发布成功！',U('post/index/single?id='.$result));
		    		};
	    		} else {
		    		$this->error('发布失败！');
	    		}
	    	} else {
	    		$this->error('请填写标题和内容');
	    	};
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function publish_pro(){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	if($_POST['title'] && $_POST['content'] && is_numeric($_POST['price'])) {
	    		$page['title'] = mc_magic_in($_POST['title']);
	    		$page['content'] = mc_magic_in(mc_str_replace_base64($_POST['content']));
	    		$page['type'] = 'pro';
	    		$page['date'] = strtotime("now");
	    		$result = M('page')->data($page)->add();
		    	if($result) {
		    		mc_add_meta($result,'term',mc_magic_in($_POST['term']));
		    		if($_POST['fmimg']) {
		    			foreach($_POST['fmimg'] as $val) {
		    				mc_add_meta($result,'fmimg',mc_save_img_base64($val,1));
		    			}
		    		};
		    		if($_POST['canshu']>0) {
			    		mc_add_meta($result ,'parameter',serialize($_POST['parameter']));
			    	} else {
				    	if($_POST['kucun']>0) {
			    			mc_add_meta($result ,'kucun',$_POST['kucun']);
			    		} else {
				    		mc_add_meta($result ,'kucun',0);
			    		};
			    	};
		    		if(is_numeric($_POST['xiaoliang'])) {
		    			mc_add_meta($result ,'xiaoliang',mc_magic_in($_POST['xiaoliang']));
		    		};
		    		if($_POST['tb_name']) {
		    			mc_add_meta($result,'tb_name',$_POST['tb_name']);
		    		};
		    		if($_POST['tb_url']) {
		    			mc_add_meta($result,'tb_url',$_POST['tb_url']);
		    		};
		    		if($_POST['keywords']) {
		    			mc_add_meta($result,'keywords',$_POST['keywords']);
		    		};
		    		if($_POST['description']) {
		    			mc_add_meta($result,'description',$_POST['description']);
		    		};
		    		if($_POST['price']>0) {
		    			mc_add_meta($result,'price',mc_magic_in($_POST['price']));
		    		};
		    		mc_add_meta($result,'author',mc_user_id());
		    		do_go('publish_pro_end',$result);
		    		$this->success('发布成功',U('control/index/pro_index'));
	    		} else {
		    		$this->error('发布失败！');
	    		}
	    	} else {
	    		$this->error('请填写标题和内容');
	    	};
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function publish_group(){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	if($_POST['title'] && $_POST['content']) {
	    		$page['title'] = mc_magic_in(mc_remove_html($_POST['title'],'all'));
	    		$page['content'] = mc_magic_in($_POST['content']);
	    		$page['type'] = 'group';
	    		$page['date'] = strtotime("now");
	    		$result = M('page')->data($page)->add();
		    	if($result) {
		    		if($_POST['fmimg']) {
		    			mc_add_meta($result,'fmimg',mc_magic_in(mc_save_img_base64($_POST['fmimg'])));
		    		};
		    		mc_add_meta($result,'author',mc_user_id());
		    		mc_add_meta(mc_user_id(),'group_admin',$result,'user');
		    		do_go('publish_group_end',$result);
		    		$this->success('新建群组成功！',U('post/group/index?id='.$result));
	    		} else {
		    		$this->error('发布失败！');
	    		}
	    	} else {
	    		$this->error('请填写群组名称和介绍');
	    	};
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function publish_article(){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	if($_POST['title'] && $_POST['content']) {
	    		$page['title'] = mc_magic_in($_POST['title']);
	    		$page['content'] = mc_magic_in(mc_str_replace_base64($_POST['content']));
	    		$page['type'] = 'article';
	    		$page['date'] = strtotime("now");
	    		$result = M('page')->data($page)->add();
		    	if($result) {
		    		if($_POST['fmimg']) {
		    			mc_add_meta($result,'fmimg',mc_magic_in(mc_save_img_base64($_POST['fmimg'])));
		    		};
		    		if(I('param.tags')) {
			    		$tags = explode(' ',I('param.tags'));
			    		foreach($tags as $tag) :
			    			if($tag) :
			    				mc_add_meta($result,'tag',$tag);
			    			endif;
			    		endforeach;
		    		};
		    		mc_add_meta($result,'term',mc_magic_in($_POST['term']));
		    		mc_add_meta($result,'author',mc_user_id());
		    		do_go('publish_article_end',$result);
		    		$this->success('发布成功！',U('control/index/article_index'));
	    		} else {
		    		$this->error('发布失败！');
	    		}
	    	} else {
	    		$this->error('请填写标题和内容');
	    	};
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function publish_topic(){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	if($_POST['title'] && $_POST['content']) {
	    		$page['title'] = mc_magic_in($_POST['title']);
	    		$page['content'] = mc_magic_in(mc_str_replace_base64($_POST['content']));
	    		$page['type'] = 'topic';
	    		$page['date'] = strtotime("now");
	    		$result = M('page')->data($page)->add();
		    	if($result) {
		    		do_go('publish_topic_end',$result);
		    		$this->success('发布成功！',U('control/index/topic_index'));
	    		} else {
		    		$this->error('发布失败！');
	    		}
	    	} else {
	    		$this->error('请填写标题和内容');
	    	};
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function edit(){
    	if(mc_is_admin() || mc_is_bianji() || mc_author_id($_POST['id'])==mc_user_id()) {
	    	if(mc_remove_html($_POST['title'],'all') && $_POST['content'] && is_numeric($_POST['id'])) {
	    		if(mc_get_page_field($_POST['id'],'type')=='pro') {
	    			if($_POST['term']) {
		    			mc_update_meta($_POST['id'],'term',mc_magic_in($_POST['term']));
		    		} else {
		    			$this->error('请设置分类！');
		    		};
	    			if($_POST['price']>0) {
	    				mc_update_meta($_POST['id'],'price',mc_magic_in($_POST['price']));
	    			} else {
						$this->error('请填写价格！');
					};
					if($_POST['canshu']>0) {
						mc_delete_meta($_POST['id'],'kucun');
						mc_update_meta($_POST['id'],'parameter',serialize($_POST['parameter']));
					} else {
						mc_delete_meta($_POST['id'],'parameter');
						if(is_numeric($_POST['kucun'])) {
			    			mc_update_meta($_POST['id'],'kucun',$_POST['kucun']);
			    		} else {
				    		mc_update_meta($_POST['id'],'kucun',0);
				    	};;
					};
		    		if(is_numeric($_POST['xiaoliang'])) {
		    			mc_update_meta($_POST['id'],'xiaoliang',$_POST['xiaoliang']);
		    		};
					if($_POST['fmimg']) {
		    			mc_delete_meta($_POST['id'],'fmimg');
		    			foreach($_POST['fmimg'] as $val) {
		    				mc_add_meta($_POST['id'],'fmimg',mc_save_img_base64($val,1));
		    			}
		    		} else {
		    			$this->error('请设置商品图片！');
		    		};
		    		mc_update_meta($_POST['id'],'tb_name',$_POST['tb_name']);
		    		mc_update_meta($_POST['id'],'tb_url',$_POST['tb_url']);
		    		mc_update_meta($_POST['id'],'keywords',$_POST['keywords']);
		    		mc_update_meta($_POST['id'],'description',$_POST['description']);
	    		};
	    		if(mc_get_page_field($_POST['id'],'type')=='group') {
	    			mc_update_meta($_POST['id'],'fmimg',mc_magic_in(mc_save_img_base64($_POST['fmimg'])));
	    		};
	    		if(mc_get_page_field($_POST['id'],'type')=='publish') {
	    			mc_update_meta($_POST['id'],'group',mc_magic_in($_POST['group']));
	    			if(mc_get_meta($_POST['id'],'number') && mc_get_page_field($_POST['group'],'type')=='pro') {
		    			mc_update_meta($_POST['id'],'buyer_phone',mc_magic_in($_POST['buyer_phone']));
					    mc_update_meta($_POST['id'],'buyer_address',mc_magic_in($_POST['buyer_address']));
					    mc_update_meta($_POST['id'],'buyer_city',mc_magic_in($_POST['buyer_city']));
					    mc_update_meta($_POST['id'],'buyer_province',mc_magic_in($_POST['buyer_province']));
					    mc_update_meta($_POST['id'],'buyer_name',mc_magic_in($_POST['buyer_name']));
	    			};
	    		};
	    		if(mc_get_page_field($_POST['id'],'type')=='article') {
	    			mc_update_meta($_POST['id'],'fmimg',mc_magic_in(mc_save_img_base64($_POST['fmimg'])));
		    		if(I('param.tags')) {
			    		mc_delete_meta($_POST['id'],'tag');
			    		$tags = explode(' ',I('param.tags'));
			    		foreach($tags as $tag) :
			    			if($tag) :
			    				mc_add_meta($_POST['id'],'tag',$tag);
			    			endif;
			    		endforeach;
		    		};
		    		if($_POST['term']) {
		    			mc_update_meta($_POST['id'],'term',mc_magic_in($_POST['term']));
		    		} else {
		    			$this->error('请设置分类！');
		    		};
	    		};
	    		$page['title'] = mc_magic_in(mc_remove_html($_POST['title'],'all'));
	    		$page['content'] = mc_magic_in(mc_remove_html(mc_str_replace_base64($_POST['content'])));
	    		M('page')->where("id='".$_POST['id']."'")->save($page);
	    		if(mc_get_page_field($_POST['id'],'type')=='pro') {
		        	$this->success('编辑成功',U('control/index/pro_index'));
	        	} elseif(mc_get_page_field($_POST['id'],'type')=='publish' || mc_get_page_field($_POST['id'],'type')=='pending') {
		        	$this->success('编辑成功',U('post/index/single?id='.$_POST['id']));
	        	} elseif(mc_get_page_field($_POST['id'],'type')=='group') {
		        	$this->success('编辑成功',U('post/group/index?id='.$_POST['id']));
	        	} elseif(mc_get_page_field($_POST['id'],'type')=='article') {
		        	$this->success('编辑成功',U('control/index/article_index'));
	        	} elseif(mc_get_page_field($_POST['id'],'type')=='topic') {
	        		$page['content'] = mc_magic_in(mc_str_replace_base64($_POST['content']));
	        		M('page')->where("id='".$_POST['id']."'")->save($page);
		        	$this->success('编辑成功',U('control/index/topic_index'));
	        	} else {
		        	$this->error('未知的Page类型',U('home/index/index'));
	        	}
	    	} else {
		    	$this->error('请完整填写信息！');
	    	}
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function add_cart($id,$number){
    	if(is_numeric($id) && is_numeric($number) && $number > 0) {
	    	if(mc_user_id()) {
	    		if($_POST['parameter']) {
	    			$parameter = unserialize(mc_get_meta($id,'parameter'));
					$kucun_par = $parameter[$_POST['parameter']]['kucun'];
				} else {
					$kucun_par = mc_get_meta($id,'kucun');
				};
	    		if($kucun_par<=0) {
	    			$this->error('商品库存不足！');
	    		} else {
		    		if($_POST['parameter']) {
				    	//$parameter = $_POST['parameter'];
						$cart = M('action')->where("page_id='".$id."' AND user_id='".mc_user_id()."' AND action_key='cart'")->getField('id',true);
						if($cart) {
							$parameter_id = M('action')->where("page_id='".$cart_id."' AND action_key='parameter' AND user_id='".mc_user_id()."' AND action_value='".mc_magic_in($_POST['parameter'])."'")->getField('id');
							if($parameter_id) {
								//如果存在此参数
								$number2 = M('action')->where("id='".$cart_id."' AND page_id='".$id."' AND user_id='".mc_user_id()."' AND action_key='cart'")->getField('action_value');
							    $action['action_value'] = $number2+$number;
							    M('action')->where("id='".$cart_id."' AND page_id='".$id."' AND action_key='cart' AND user_id='".mc_user_id()."'")->save($action);
							} else {
								//如果参数不存在
								$result = mc_add_action($id,'cart',$number);
					    		if($result) {
						    		mc_add_action($result,'parameter',mc_magic_in($_POST['parameter']));
					    		};
							};
				    	} else {
					    	//购物车中不存在本商品
					    	$result = mc_add_action($id,'cart',$number);
				    		if($result) {
					    		mc_add_action($result,'parameter',mc_magic_in($_POST['parameter']));
				    		}
				    	}
		    		} else {
			    		//本商品不存在多种型号
			    		$cart = M('action')->where("page_id='".mc_magic_in($id)."' AND user_id='".mc_user_id()."' AND action_key='cart'")->getField('id',true);
			    		if($cart) {
				    		foreach($cart as $cart_id) {
					    		$par_old = M('action')->where("page_id='".mc_magic_in($cart_id)."' AND user_id='".mc_user_id()."'")->getField('id',true);
					    		if($par_old) {
						    		//购物车内商品存在参数
					    		} else {
						    		$number2 = M('action')->where("id='".mc_magic_in($cart_id)."' AND page_id='".mc_magic_in($id)."' AND user_id='".mc_user_id()."' AND action_key='cart'")->getField('action_value');
						    		$action['action_value'] = $number2+$number;
									M('action')->where("id='".mc_magic_in($cart_id)."' AND page_id='".mc_magic_in($id)."' AND action_key='cart' AND user_id='".mc_user_id()."'")->save($action);
					    		}
				    		}
			    		} else {
				    		//购物车内不存在相同商品
				    		$result = mc_add_action($id,'cart',$number);
			    		}
		    		}
		    		if($_POST['back']==1) :
		    			$this->success('加入购物车成功',U('pro/index/single?id='.$id));
		    		else :
		    			$this->success('加入购物车成功',U('pro/cart/index'));
		    		endif;
	    		}
	    	} else {
			    $this->success('请先登陆',U('user/login/index'));
		    }
	    } else {
		    $this->error('参数错误！');
	    }
    }
    public function cart_delete($id){
    	if(is_numeric($id)) {
	    	M('action')->where("id='$id' AND user_id='".mc_user_id()."'")->delete();
	    	M('action')->where("page_id='$id' AND user_id='".mc_user_id()."' AND action_key='parameter'")->delete();
			$this->success('删除成功',U('pro/cart/index'));
    	} else {
	    	$this->error('参数错误！');
    	}
    }
    public function nav_del($id){
    	if(mc_is_admin()) {
    		if(is_numeric($id)) {
	    		$condition['type']  = array('in',array('nav','nav2','nav3','nav4'));
	    		$condition['id'] = $id;
	    		M('option')->where($condition)->delete();
	    		$this->success('删除导航成功');
    		} else {
	    		$this->error('参数错误！');
    		}
    	} else {
	    	$this->error('哥们，请不要放弃治疗！',U('Home/index/index'));
    	}
    }
    public function comment_delete($id){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	if(is_numeric($id)) {
		    	M('action')->where("id='$id' AND action_key='comment'")->delete();
		    	M('action')->where("action_value='$id' AND action_key='at'")->delete();
		    	$this->success('删除成功');
		    } else {
		    	$this->error('参数错误！');
	    	}
    	} else {
	    	$this->error('哥们，请不要放弃治疗！',U('Home/index/index'));
    	}
    }
    public function publish_term(){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	if($_POST['title']) {
	    		$page['title'] = mc_magic_in($_POST['title']);
	    		$page['type'] = 'term_'.mc_magic_in($_POST['type']);
	    		$page['date'] = strtotime("now");
	    		$result = M('page')->data($page)->add();
		    	if($result) {
		    		if(is_numeric($_POST['parent'])) {
			    		mc_add_meta($result,'parent',$_POST['parent'],'term');
		    		};
		    		$this->success('新建分类成功！');
	    		} else {
		    		$this->error('发布失败！');
	    		}
	    	} else {
	    		$this->error('请填写分类名称');
	    	};
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function edit_term($id){
    	if(mc_is_admin() && is_numeric($id)) {
	    	if($_POST['title']) {
	    		$page['title'] = mc_magic_in($_POST['title']);
	    		if($_POST['paixu']>0) {
		    		$page['date'] = strtotime("now");
	    		};
	    		M('page')->where("id='$id'")->save($page);
	    		$type = mc_get_page_field($id,'type');
	    		if($type=='term_pro') {
		    		if(is_numeric($_POST['parent'])) {
		    			if($_POST['parent']==$id) {
			    			$this->error('父分类不能为自己！');
		    			} else {
			    			if(mc_get_meta($id,'parent',true,'term')) {
				    			mc_update_meta($id,'parent',$_POST['parent'],'term');
			    			} else {
				    			mc_add_meta($id,'parent',$_POST['parent'],'term');
			    			}
		    			}
		    		} else {
		    			mc_delete_meta($id,'parent','term');
		    		};
		    		$type_name = 'pro';
	    		} elseif($type=='term_baobei') {
	    			$type_name = 'baobei';
	    		};
		    	$this->success('编辑分类成功！');
	    	} else {
	    		$this->error('请填写分类名称');
	    	};
	    } else {
		    $this->error('哥们，你放弃治疗了吗?',U('home/index/index'));
	    };
    }
    public function qiandao(){
    	if(mc_is_qiandao()) {
	    	$this->error('您已签到过了哦～');
	    } else {
	    	if(mc_user_id()) {
		    	$coins = 3;
		    	mc_update_coins(mc_user_id(),$coins);
		    	mc_add_action(mc_user_id(),'coins',$coins);
		    	$this->success('签到成功！',U('home/index/index'));
		    } else {
			    $this->success('请先登陆',U('user/login/index'));
		    }
    	}
    }
    public function review($id){
	    if(mc_is_admin() || mc_is_bianji()) {
	    	$type = mc_get_page_field($id,'type');
	    	if($type=='pending') {
		    	mc_update_page($id,'publish','type');
		    	$this->success('审核成功！',U('post/index/single?id='.$id));
	    	} else {
		    	$this->error('未知页面类型');
	    	}
	    } else {
		    $this->error('请不要放弃治疗');
	    }
    }
    public function delete($id){
        if(is_numeric($id)) {
	        if(mc_is_admin()) {
		         if(mc_get_meta($id,'user_level',true,'user')!=10) {
		         	 $type = mc_get_page_field($id,'type');
		         	 if($type=='pro') {
		         	 	mc_update_page($id,'pro_recycle','type');
		         	 	$this->success('商品已移到回收站',U('control/index/pro_index'));
		         	 } elseif($type=='article') {
		         	 	mc_delete_page($id);
					 	$this->success('删除成功',U('control/index/article_index'));
		         	 } elseif($type=='topic') {
		         	 	mc_delete_page($id);
					 	$this->success('删除成功',U('control/index/topic_index'));
		         	 } elseif($type=='user') {
		         	 	mc_delete_page($id);
					 	$this->success('删除成功',U('control/index/manage'));
		         	 } else {
			         	 mc_delete_page($id);
					 	 $this->success('删除成功',U('Home/index/index'));
		         	 };
		         } else {
			         $this->error('请不要伤害管理员');
		         };
	        } else {
	        	$this->error('哥们，请不要放弃治疗！',U('Home/index/index'));
	        }
        } else {
	        $this->error('参数错误！');
        }
    }
    public function delete_img($id){
        if(is_numeric($id)) {
	        if(mc_is_admin()) {
		         $src = M('attached')->where("id='$id'")->getField('src');
			     M('attached')->where("id='$id'")->delete();
			     $src = str_replace(mc_site_url().'/', '', $src);
			     unlink($src);
			     $this->success('删除成功');
	        } else {
	        	$this->error('哥们，请不要放弃治疗！',U('Home/index/index'));
	        }
        } else {
	        $this->error('参数错误！');
        }
    }
    public function ip_false($id){
        if(is_numeric($id)) {
	        if(mc_is_admin()) {
		         if(mc_get_meta($id,'user_level',true,'user')!=10) {
			         $ip_array = M('action')->where("page_id='$id' AND action_key='ip'")->getField('action_value',true);
			         if($ip_array) {
				        foreach($ip_array as $ip) {
					        mc_add_option('ip_false',$ip,'user');
				        };
			         };
			         mc_delete_page($id);
			         $this->success('操作成功',U('Home/index/index'));
		         } else {
			         $this->error('请不要伤害管理员');
		         };
	        } else {
	        	$this->error('哥们，请不要放弃治疗！',U('Home/index/index'));
	        }
        } else {
	        $this->error('参数错误！');
        }
    }
    public function zhiding($id){
	    if(mc_is_admin() || mc_is_bianji()) {
	    	if(is_numeric($id)) {
		    	$time = strtotime("now")+846000;
			    mc_update_page($id,$time,'date');
		    };
		    $this->success('置顶成功！',mc_get_url($id));
	    } else {
		    $this->error('请不要放弃治疗');
	    }
    }
    public function cart_number($id,$number,$for=false){
	    if(mc_user_id()) {
	    	if(is_numeric($id) && is_numeric($number)) {
		    	$cart_number = M('action')->where("id='$id'")->getField('action_value');
		    	if(mc_cart_kucun($id)>$cart_number) {
			    	$number_now = M('action')->where("user_id='".mc_user_id()."' AND action_key='cart' AND id='$id'")->getField('action_value');
		    		if($for) {
			    		$number = -$number;
		    		};
		    		$action['action_value'] = $number_now+$number;
			    	M('action')->where("user_id='".mc_user_id()."' AND action_key='cart' AND id='$id'")->save($action);
			    	$this->success('更改数量成功！');
		    	} else {
			    	$action['action_value'] = mc_cart_kucun($id);
			    	M('action')->where("user_id='".mc_user_id()."' AND action_key='cart' AND id='$id'")->save($action);
			    	$this->success('商品库存不足！');
		    	};
	    	} else {
		    	$this->error('参数错误！');
	    	}
	    } else {
		    $this->error('请先登陆！');
	    }
    }
    public function tuisong($id,$fmimg){
	    if(mc_is_admin() || mc_is_bianji()) {
	    	if(is_numeric($id) && $fmimg) {
		    	mc_add_meta($id,'tuisong',mc_save_img_base64($fmimg));
	    	};
		    $this->success('推送成功！',mc_get_url($id));
	    } else {
		    $this->error('请不要放弃治疗');
	    }
    }
    public function remts($id){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	if(is_numeric($id)) {
		    	mc_delete_meta($id,'tuisong');
	    	};
		    $this->success('取消推送成功！',mc_get_url($id));
	    } else {
		    $this->error('请不要放弃治疗');
	    }
    }
    public function pro_up($id){
	    //mc_prev_page_id($id)
	    if(mc_is_admin() || mc_is_bianji()) {
		    $pro_id = mc_next_page_id($id);
		    if($pro_id>0) {
			    $date = mc_get_page_field($pro_id,'date');
			    $page['date'] = $date+1;
				M('page')->where("id='$id' AND type='pro'")->save($page);
			};
			$this->success('操作成功！');
		} else {
			$this->error('请不要放弃治疗');
		};
    }
    public function pro_down($id){
	    if(mc_is_admin() || mc_is_bianji()) {
		    $pro_id = mc_prev_page_id($id);
		    if($pro_id>0) {
			    $date = mc_get_page_field($pro_id,'date');
			    $page['date'] = $date-1;
				M('page')->where("id='$id' AND type='pro'")->save($page);
			};
			$this->success('操作成功！');
		} else {
			$this->error('请不要放弃治疗');
		};
    }
}