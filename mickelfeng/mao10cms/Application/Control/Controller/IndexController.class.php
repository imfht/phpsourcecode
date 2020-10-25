<?php
namespace Control\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function home(){
		$url = mc_site_url().'/index.php?m=control&c=index&a=index';
		Header("Location:$url");
	}
    public function index(){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		$mao10cms = 'http://www.mao10.com';
	    		$version = unserialize(file_get_contents($mao10cms."/site_update/version.txt"));
	    		if(mc_option('site_version')=='') {
		    		mc_add_option('site_version','3.3');
	    		};
    			if($_GET['update']=='mao10cms') {
	    			$special1 = unserialize(file_get_contents($mao10cms."/site_update/special1.txt")); //文件夹
	    			$special2 = unserialize(file_get_contents($mao10cms."/site_update/special2.txt"));	//文件和升级文件名
	    			foreach($special1 as $val) {
	    				$filename = THINK_PATH.'../'.$val;
	    				if (!is_dir($filename)){
							mkdir($filename, 0777);
						} else {
							@chmod($filename, 0777);
						};
	    			};
	    			foreach($special2 as $val) {
	    				list($name1,$name2) = explode('|',$val);
		    			$filename = THINK_PATH.'../'.$name1;
		    			$file_code = file_get_contents($mao10cms."/site_update/patch/".$name2);
						if (!file_exists($filename)) {
							if (!is_writeable($filename)) {
								@chmod($filename, 0777);
							};
							file_put_contents($filename,$file_code);
						};
	    			};
	    			mc_update_option('site_version',$version['ver']);
    			};
    			$this->assign('version',$version);
	    		$this->theme('admin')->display('Control/index');
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function set(){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		if($_POST['site_name'] && $_POST['site_url'] && $_POST['page_size']) {
		    		mc_update_option('site_name',I('param.site_name'));
		    		mc_update_option('site_url',I('param.site_url'));
		    		mc_update_option('site_color',I('param.site_color'));
		    		mc_update_option('theme',$_POST['theme']);
		    		mc_update_option('pro_name',$_POST['pro_name']);
		    		mc_update_option('group_name',$_POST['group_name']);
		    		mc_update_option('article_name',$_POST['article_name']);
		    		mc_update_option('logo',mc_save_img_base64($_POST['logo']));
		    		mc_update_option('stmp_from',I('param.stmp_from'));
		    		mc_update_option('stmp_name',I('param.stmp_name'));
		    		mc_update_option('stmp_host',I('param.stmp_host'));
		    		mc_update_option('stmp_port',I('param.stmp_port'));
		    		mc_update_option('stmp_username',I('param.stmp_username'));
		    		mc_update_option('stmp_password',I('param.stmp_password'));
		    		mc_update_option('fmimg',mc_save_img_base64($_POST['fmimg']));
		    		mc_update_option('homehdimg1',mc_save_img_base64($_POST['homehdimg1']));
		    		mc_update_option('homehdimg2',mc_save_img_base64($_POST['homehdimg2']));
		    		mc_update_option('homehdimg3',mc_save_img_base64($_POST['homehdimg3']));
		    		mc_update_option('homehdlnk1',I('param.homehdlnk1'));
		    		mc_update_option('homehdlnk2',I('param.homehdlnk2'));
		    		mc_update_option('homehdlnk3',I('param.homehdlnk3'));
		    		mc_update_option('homehdtitle1',I('param.homehdtitle1'));
		    		mc_update_option('homehdtitle2',I('param.homehdtitle2'));
		    		mc_update_option('homehdtitle3',I('param.homehdtitle3'));
		    		mc_update_option('homehdtext1',I('param.homehdtext1'));
		    		mc_update_option('homehdtext2',I('param.homehdtext2'));
		    		mc_update_option('homehdtext3',I('param.homehdtext3'));
		    		mc_update_option('homehdbtn1',I('param.homehdbtn1'));
		    		mc_update_option('homehdbtn2',I('param.homehdbtn2'));
		    		mc_update_option('homehdbtn3',I('param.homehdbtn3'));
		    		mc_update_option('page_size',I('param.page_size'));
		    		mc_update_option('shehe_comment',I('param.shehe_comment'));
		    		mc_update_option('upyun',I('param.upyun'));
		    		mc_update_option('upyun_url',I('param.upyun_url'));
		    		mc_update_option('upyun_bucket',I('param.upyun_bucket'));
		    		mc_update_option('upyun_user',I('param.upyun_user'));
		    		mc_update_option('upyun_pwd',I('param.upyun_pwd'));
		    		mc_update_option('loginqq',I('param.loginqq'));
		    		mc_update_option('loginqq_appid',I('param.loginqq_appid'));
		    		mc_update_option('loginqq_appkey',I('param.loginqq_appkey'));
		    		$loginqq = '<?php die("forbidden"); ?>
{"appid":"'.I('param.loginqq_appid').'","appkey":"'.I('param.loginqq_appkey').'","callback":"'.I('param.site_url').'/connect-qq","scope":"get_user_info","errorReport":true,"storageType":"file","host":"localhost","user":"root","password":"root","database":"test"}';
					$fileName = THINK_PATH.'../connect-qq/API/comm/inc.php';
					if (!is_writeable($fileName)) {
						@chmod($fileName, 0777);
					};
					file_put_contents($fileName, $loginqq);
					mc_update_option('loginweibo',I('param.loginweibo'));
		    		mc_update_option('loginweibo_appid',I('param.loginweibo_appid'));
		    		mc_update_option('loginweibo_appkey',I('param.loginweibo_appkey'));
		    		$loginweibo = "<?php header('Content-Type: text/html; charset=UTF-8'); define( 'WB_AKEY' , '".I('param.loginweibo_appid')."' ); define( 'WB_SKEY' , '".I('param.loginweibo_appkey')."' ); define( 'WB_CALLBACK_URL' , '".I('param.site_url')."/connect-weibo' );";
					$fileName2 = THINK_PATH.'../connect-weibo/config.php';
					if (!is_writeable($fileName2)) {
						@chmod($fileName2, 0777);
					};
					file_put_contents($fileName2, $loginweibo);
		    		$this->success('更新成功');
	    		} else {
		    		$this->theme('admin')->display('Control/set');
	    		}
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function pro_all($page=1){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
		    	if($_POST['date'] && $_POST['wuliu'] && $_POST['user_id']) {
	    			M('action')->where("page_id='".$_POST['user_id']."' AND user_id='".$_POST['user_id']."' AND action_key='wl_wait_finished' AND date = '".$_POST['date']."'")->delete();
	    			$action['page_id'] = $_POST['user_id'];
					$action['user_id'] = $_POST['user_id'];
					$action['action_key'] = 'wl_wait_finished';
					$action['action_value'] = $_POST['wuliu'];
					$action['date'] = $_POST['date'];
					$result = M('action')->data($action)->add();
	    			$this->success('保存成功');
    			} else {
	    			if($_GET['type']) {
		    			$condition['action_key'] = 'trade_wait_'.$_GET['type'];
	    			} else {
		    			$condition['action_key'] = array('in',array('trade_wait_send','trade_wait_cofirm','trade_wait_finished','trade_wait_hdfk'));
	    			};
	    			$this->page = M('action')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
	    			$count = M('action')->where($condition)->count();
			        $this->assign('id',$id);
			        $this->assign('count',$count);
			        $this->assign('page_now',$page);
					$this->theme('admin')->display('Control/pro_all');
    			}
	    	} else {
		    	$this->error('请不要偷窥别人的购买记录哦～');
	    	}
	    } else {
		    $this->success('请先登陆',U('User/login/index'));
	    }
    }
    public function paytools(){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		if($_POST['update_paytools']) {
		    		mc_update_option('alipay2_seller',I('param.alipay2_seller'));
		    		mc_update_option('alipay2_partner',I('param.alipay2_partner'));
		    		mc_update_option('alipay2_key',I('param.alipay2_key'));
		    		mc_update_option('alipay_seller',I('param.alipay_seller'));
		    		mc_update_option('alipay_partner',I('param.alipay_partner'));
		    		mc_update_option('alipay_key',I('param.alipay_key'));
		    		mc_update_option('alipay_wap_seller',I('param.alipay_wap_seller'));
		    		mc_update_option('alipay_wap_partner',I('param.alipay_wap_partner'));
		    		mc_update_option('alipay_wap_key',I('param.alipay_wap_key'));
		    		mc_update_option('tenpay_seller',I('param.tenpay_seller'));
		    		mc_update_option('tenpay_key',I('param.tenpay_key'));
		    		mc_update_option('huodaofukuan',I('param.huodaofukuan'));
		    		mc_update_option('yunfei',I('param.yunfei'));
		    		mc_update_option('jifen',I('param.jifen'));
		    		$this->success('更新成功');
	    		} else {
		    		$this->theme('admin')->display('Control/paytools');
	    		}
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function manage($page=1){
    	if(is_numeric($page)) {
	    	if(mc_user_id()) {
	    		if(mc_is_admin()) {
	    			if(is_numeric($_POST['user_level']) && is_numeric($_POST['user_id'])) {
		    			if($_POST['user_id']==mc_user_id()) {
			    			$this->error('您不能修改自己的身份！',U('Control/index/manage'));
		    			} else {
			    			mc_update_meta($_POST['user_id'],'user_level',$_POST['user_level'],'user');
							$this->success('修改用户身份成功！');
		    			};
	    			} else {
		    			$this->page = M('page')->where("type='user'")->order('id desc')->page($page,mc_option('page_size'))->select();
						$count = M('page')->where("type='user'")->count();
						$this->assign('count',$count);
						$this->assign('page_now',$page);
						$this->theme('admin')->display('Control/manage');
	    			}
		    	} else {
			    	$this->error('您没有权限访问此页面！');
		    	};
	    	} else {
		    	$this->success('请先登陆',U('User/login/index'));
		    };
		} else {
			$this->error('参数错误！');
		}
    }
    public function tixian(){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		if($_POST['id'] && $_POST['zhuangtai']) {
		    		$condition['action_value'] = $_POST['zhuangtai'];
		    		M('action')->where("id='".$_POST['id']."'")->save($condition);
		    		if($_POST['zhuangtai']==3) {
			    		$user_id = M('action')->where("id='".$_POST['id']."'")->getField('page_id');
			    		$date = M('action')->where("id='".$_POST['id']."'")->getField('date');
			    		$coins = M('action')->where("date='$date' AND action_key='coins'")->getField('action_value');
			    		mc_update_coins($user_id,-$coins);
		    		};
		    		$this->success('修改提现状态成功！');
	    		} else {
		    		$condition['action_value']  = array('lt',0);
			        $condition['action_key'] = 'coins';
					$this->page = M('action')->where($condition)->order('id desc')->page($page,mc_option('page_size'))->select();
				    $count = M('action')->where($condition)->count();
				    $this->assign('id',$id);
				    $this->assign('count',$count);
				    $this->assign('page_now',$page);
		    		$this->theme('admin')->display('Control/tixian');
	    		}
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function images($page=1){
    	if(mc_user_id()) {
    		if(mc_is_admin()) {
	    		$this->content = M('attached')->order('id desc')->page($page,20)->select();
			    $count = M('attached')->where($condition)->count();
			    $this->assign('count',$count);
			    $this->assign('page_now',$page);
			    $this->theme('admin')->display('Control/images');
	    	} else {
		    	$this->error('您没有权限访问此页面！');
	    	};
    	} else {
	    	$this->success('请先登陆',U('User/login/index'));
	    };
    }
    public function pro_index($page=1){
        if(!is_numeric($page)) {
	        $this->error('参数错误');
        }
        if($_GET['keyword']) {
	        $where['content']  = array('like', "%{$_GET['keyword']}%");
			$where['title']  = array('like',"%{$_GET['keyword']}%");
			$where['_logic'] = 'or';
			$condition['_complex'] = $where;
			$condition['type']  = 'pro';
	        $this->page = M('page')->where($condition)->order('date desc')->page($page,30)->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme('admin')->display('Pro/index');
        } else {
	        $condition['type'] = 'pro';
	        $this->page = M('page')->where($condition)->order('date desc')->page($page,30)->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme('admin')->display('Pro/index');
	    };
    }
    public function pro_recycle($page=1){
    	if(!is_numeric($page)) {
	        $this->error('参数错误');
        } else {
	        $condition['type'] = 'pro_recycle';
	        $this->page = M('page')->where($condition)->order('id desc')->page($page,30)->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme('admin')->display('Pro/recycle');
	    };
    }
    public function pro_term($id,$page=1){
    	if(is_numeric($id) && is_numeric($page)) {
	    	//检索子分类
        	$args_id_t = M('meta')->where("meta_key='parent' AND meta_value='$id' AND type='term'")->getField('page_id',true);
        	if($args_id_t) {
				$condition_t['id']  = array('in',$args_id_t);
				$condition_t['type']  = 'term_pro';
				$terms_pro_t = M('page')->where($condition_t)->getField('id',true);
			};
			if($terms_pro_t) {
				//如果有子分类，获取子分类下商品
				$condition_child['meta_key'] = 'term';
				$condition_child['meta_value'] = array('in',$terms_pro_t);
				$condition_child['type'] = 'basic';
				$args_id_child = M('meta')->where($condition_child)->getField('page_id',true);
				//获取当前分类下商品
				$args_id_this = M('meta')->where("meta_key='term' AND meta_value='$id' AND type='basic'")->getField('page_id',true);
				if($args_id_child && $args_id_this) {
					$args_id = array_merge($args_id_child,$args_id_this);
				} elseif($args_id_this) {
					$args_id = $args_id_this;
				} elseif($args_id_child) {
					$args_id = $args_id_child;
				}
			} else {
				//如果没有子分类，直接获取当前分类下商品
				$args_id = M('meta')->where("meta_key='term' AND meta_value='$id' AND type='basic'")->getField('page_id',true);
			}
			if($args_id) {
				$condition['id']  = array('in',$args_id);
				$condition['type'] = 'pro';
		    	$this->page = M('page')->where($condition)->order('date desc')->page($page,30)->select();
			    $count = M('page')->where($condition)->count();
		    };
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme('admin')->display('Pro/term');
		} else {
			$this->error('参数错误！');
		}
	}
    public function article_index($page=1){
        if(!is_numeric($page)) {
	        $this->error('参数错误');
        }
        if($_GET['keyword']) {
	        $where['content']  = array('like', "%{$_GET['keyword']}%");
			$where['title']  = array('like',"%{$_GET['keyword']}%");
			$where['_logic'] = 'or';
			$condition['_complex'] = $where;
			$condition['type']  = 'article';
	        $this->page = M('page')->where($condition)->order('id desc')->page($page,30)->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme('admin')->display('Article/index');
        } else {
	        $condition['type'] = 'article';
	        $this->page = M('page')->where($condition)->order('id desc')->page($page,30)->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme('admin')->display('Article/index');
	    };
    }
    public function article_term($id,$page=1){
    	if(is_numeric($id) && is_numeric($page)) {
	    	$condition['type'] = 'article';
        	$args_id = M('meta')->where("meta_key='term' AND meta_value='$id' AND type='basic'")->getField('page_id',true);
        	if($args_id) {
	        	$condition['id']  = array('in',$args_id);
		    	$this->page = M('page')->where($condition)->order('date desc')->page($page,30)->select();
			    $count = M('page')->where($condition)->count();
		    };
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme('admin')->display('Article/term');
		} else {
			$this->error('参数错误！');
		}
	}
	public function post_pending($page=1){
    	if(mc_is_admin() || mc_is_bianji()) {
	    	$this->page = M('page')->where('type="pending"')->order('id desc')->page($page,mc_option('page_size'))->select();
		    $count = M('page')->where('type="pending"')->count();
		    $this->assign('id',$id);
		    $this->assign('count',$count);
		    $this->assign('page_now',$page);
			$this->theme('admin')->display('Post/pending');
		} else {
			$this->error('你没有权限查看此页面！');
		}
	}
    public function module(){
    	$this->theme('admin')->display('Control/module');
    }
    public function nav(){
    	$this->theme('admin')->display('Control/nav');
    }
    public function topic_index($page=1){
        if(!is_numeric($page)) {
	        $this->error('参数错误');
        }
        if($_GET['keyword']) {
	        $where['content']  = array('like', "%{$_GET['keyword']}%");
			$where['title']  = array('like',"%{$_GET['keyword']}%");
			$where['_logic'] = 'or';
			$condition['_complex'] = $where;
			$condition['type']  = 'topic';
	        $this->page = M('page')->where($condition)->order('id desc')->page($page,30)->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme('admin')->display('Topic/index');
        } else {
	        $condition['type'] = 'topic';
	        $this->page = M('page')->where($condition)->order('id desc')->page($page,30)->select();
	        $count = M('page')->where($condition)->count();
	        $this->assign('count',$count);
	        $this->assign('page_now',$page);
	        $this->theme('admin')->display('Topic/index');
	    };
    }
    public function mysql(){
	    if(mc_is_admin()) {
		    $tables = array('option','page','meta','action','attached');
		    $fileName = THINK_PATH.'../db.php';
		    if (!is_writeable($fileName)) {
				@chmod($fileName, 0777);
			};
			$json = array();
		    foreach($tables as $table) :
		    	$mysql = M($table)->order('id desc')->select();
		    	$json[$table] = serialize($mysql);
		    endforeach;
		    file_put_contents($fileName, $json);
		    $this->success('数据导出成功！',mc_site_url());
		} else {
			$this->error('凡人，请远离是非之地！',mc_site_url());
		}
	}
    public function mysqlin(){
	    if(mc_is_admin()) {
		    $fileName = THINK_PATH.'../db.php';
		    if (!is_writeable($fileName)) {
				@chmod($fileName, 0777);
			};
		    $json = file_get_contents($fileName);
		    $mysqls = unserialize($json);
		    foreach($mysqls as $table=>$mysql) :
		    	M($table)->data($mysql)->add();
		    endforeach;
		    $this->success('数据导入成功！',mc_site_url());
		} else {
			$this->error('凡人，请远离是非之地！',mc_site_url());
		}
	}
}