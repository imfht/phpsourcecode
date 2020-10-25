<?php
namespace app\common\controller;

use think\Controller;
use app\common\model\User;
use app\common\model\Arctype;
use app\common\model\Navigation;
use app\common\model\Config;
use app\common\model\TokenUser;
use app\common\model\Music;
use app\common\model\Template;

// 基础控制器

class Base extends Controller {

	//当前 UID
	public $uid;

    public function initialize() {
        $this->uid = session('userId');
    	define('H_NAME', request()->domain());	//获取当前域名,包含"http://"
        define('M_NAME', request()->module());	//当前模块名称
        define('C_NAME', request()->controller());	//当前控制器名称
        define('A_NAME', request()->action());	//当前操作名称
        $box_is_pjax = request()->isPjax();
        $this->assign('box_is_pjax', $box_is_pjax);
		$this->set_template();	// 模版设置
        $this->assign('empty', '<div class="media box box-solid x-p-10"><div class="media-body text-center">暂时没有数据</div></div>');   //没有数据模版
		if( !empty($this->uid) ){
			$user = User::get( ['id' => $this->uid] );
			$user->userInfo;
			$this->assign('user',$user);
		}else{
			$this->assign('user',$user['id']=0);
		}
		$this->jiaZaiDaoHang();
		$music = new Music();
		$bd_yinyue = $music->where(['status'=>1])->order('orderby ASC,id desc')->select();
		$bd_yinyue = json_encode($bd_yinyue);	//音乐源,本地

		$this->assign('is_music',confv('is_music','music'));	//是否开启音乐播放器
		$this->assign('is_AutoPlay',confv('is_AutoPlay','music'));	//是否自动播放
		$music_type = confv('music_type','music');	//音乐数据类型,网易/本地
		$wy_yinyue = confv('wy_yinyue','music');	//音乐源,网易
		if( $music_type == 'file' ){
			if( !empty($bd_yinyue) ){
				$this->assign('music_type',$music_type);
				$this->assign('YinYueData',$bd_yinyue);
			}else{
				$this->assign('YinYueData',$wy_yinyue);
				$this->assign('music_type','cloud');
			}
		}else{
			$this->assign('music_type',$music_type);
			$this->assign('YinYueData',$wy_yinyue);
		}
        $is_login = $this->restLogin();
		if( !empty($is_login) ){
			return true;
		}

	}

	protected function jiaZaiDaoHang(){//加载导航
		$Navigation = new Navigation();
		if(!empty($this->uid)){
			$this->assign('mDaoHangList', $Navigation->daoHang( [2,4]) );//网站导航/会员中心网站导航
		}else{
			$this->assign('mDaoHangList', $Navigation->daoHang([2]) );//网站导航
		}
		$list = cache('DB_COMMIN_ARCTYPE');
		if(!$list){
	        $list = Arctype::where(['status'=>1,'is_daohang'=>1])->order('sorts ASC,id ASC')->select();
	        foreach ($list as $k => $v){
	            $v->arctypeMod;
				$lists = Arctype::where(['pid'=>$v['id'],'status'=>1,'is_daohang'=>1])->order('sorts ASC,id ASC')->count();
				if( $lists > 0 ){
					$v['dirs'] = null;
					$v['jumplink'] = null;
				}
	        }
	        $treeClass = new \expand\Tree();
	        $list = $treeClass->create($list);
			cache('DB_COMMIN_ARCTYPE', $list);
		}
		$this->assign('arcList', $list );	//文章导航
	}

    protected function restLogin() {
        if (empty($this->uid)){   //未登录
            session('userId', null);
        	session('user_token', null);
			return '请登录';
        }
        $config = new Config();
        $login_time = $config->where(['type'=>'system', 'k'=>'login_time'])->value('v');
        $now_token = session('user_token');   //当前token
        $tkModel = new TokenUser();
        $db_token = $tkModel->where(['uid'=>$this->uid, 'type'=>'1'])->find();   //数据库token
        if ($db_token['token'] != $now_token){   //其他地方登录
            session('userId', null);
        	session('user_token', null);
			return '其他地方登录';
        }else{
            if ($db_token['token_time'] < time()){   //登录超时
                session('userId', null);
        		session('user_token', null);
				return '登录超时';
            }else{
                $token_time = time() + $login_time;
                $data = ['token_time' => $token_time];
                $tkModel->where(['uid'=>$this->uid, 'type'=>'1'])->update($data);
				return false;
            }
        }
    }

	protected function set_template(){	// 模版设置
		$temp = Template::get(['status'=>1]);
		$is_mobile = confv('is_mobile','web');
		$module = strtolower(request()->module());	//当前模型
		$puth = './template/'.$temp['puth_name'].'/'.$module;	// 模版目录
		$static = ['__static__' => '/template/'.$temp['puth_name'].'/static' ];	// 静态文件 css/js/图片文件
		$static_default = ['__static__' => '/template/default/static' ];	// 默认静态文件 css/js/图片文件
		$mobile_puth = './template/mobile';	// 手机端目录
		$c_a = strtolower('/' . request()->controller() . '/' . request()->action());	// 当前控制器和操作方法名
		if( request()->isMobile() ){	// 手机访问
			$is_file = file_exists($mobile_puth);	// 检测手机端模板目录
			if( $is_mobile == 1 && $is_file == true ){	// 开启使用手机模版和mobile目录存在
				if( file_exists($mobile_puth.'/'.$module.$c_a.'.html') ){
					$this->view->config('view_path', $mobile_puth.'/'.$module.'/');
					$this->view->config('tpl_replace_string',$static);	// 静态文件 css/js/图片文件
				}else{
					if( file_exists('./template/default/mobile/'.$module.$c_a.'.html') ){
						$this->view->config('view_path', './template/default/mobile/'.$module.'/');	// 使用默认手机端模版
					}else{
						$this->view->config('view_path', './template/default/'.$module.'/');	// 使用默认PC端模版
					}
					$this->view->config('tpl_replace_string',$static_default);	// 默认静态文件 css/js/图片文件
				}
			}else{
				if( file_exists($puth) ){
					$this->view->config('view_path', $puth.'/');
					$this->view->config('tpl_replace_string',$static);	// 静态文件 css/js/图片文件
				}else{
					$this->view->config('view_path', './template/default/'.$module.'/');	// 使用默认PC端模版
					$this->view->config('tpl_replace_string',$static_default);	// 默认静态文件 css/js/图片文件
				}
			}
		}else{	// 	PC访问
			if( file_exists($puth.$c_a.'.html') ){
				$this->view->config('view_path', $puth.'/');
				$this->view->config('tpl_replace_string',$static);	// 静态文件 css/js/图片文件
			}else{
				$this->view->config('view_path', './template/default/'.$module.'/');	// 使用默认PC端模版
				$this->view->config('tpl_replace_string',$static_default);	// 默认静态文件 css/js/图片文件
			}
		}

	}
}
