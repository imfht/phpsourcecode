<?php
namespace app\common\behavior;
use app\common\model\Config as ConfigModel;
/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\behavior
 */
class Init{
	private $webdb;

	public function run(&$params){
		// 如果是安装系统，不需要执行
		if(defined('BIND_MODULE')&&BIND_MODULE=='install') return;
		if(empty(get_cookie('user_sid'))){    //分配每个用户一个唯一字串
			set_cookie('user_sid',rands(10));
		}
		if(input('get.in')!=''){
			set_cookie('browser_type',input('get.in'));
		}
		if(input('get.in')=='pc'||get_cookie('browser_type')=='pc'){
			//define('IN_WAP',false);
		}elseif(input('get.in')=='wap'||get_cookie('browser_type')=='wap'){
			if(ENTRANCE!=='admin'){  //后台,强制不要用WAP
				define('IN_WAP',true);
			}
		}elseif(in_wap()){
			define('IN_WAP',true);
		}
		//define('IN_WAP',true);
		if(IN_WAP===true){
			config('template.cache_prefix','wap_');
		}else{
			config('paginate',['type'=>'page\Pc_page','var_page'=>'page','list_rows'=>20]);	//分页参数设置
		}
		define('TEMPLATE_PATH',ROOT_PATH.'template/');
		define('IS_POST',request()->isPost()?true:false);
		// 获取前台访问网址，是否放在根目录
		$base_file=request()->baseFile();
		$web_path=substr($base_file,0,strripos($base_file,'/')+1);
		define('PUBLIC_URL',$web_path.'public/');                          //静态文件访问网址,是浏览器访问的网址,不是硬盘路径
		define('PUBLIC_PATH',ROOT_PATH.'public'.DS);                       //静态文件硬盘路径,是磁盘路径,不是浏览器的访问路径
		define('UPLOAD_URL',PUBLIC_URL.'uploads/');                         //上传的附件访问网址,是浏览器访问的网址,不是硬盘路径
		define('UPLOAD_PATH',PUBLIC_PATH.'uploads'.DS);                       //上传的附件硬盘路径,是磁盘路径,不是浏览器的访问路径
		define('STATIC_URL',PUBLIC_URL.'static/');                         //图片JS及CSS的访问网址,是浏览器访问的网址,不是硬盘路径
		define('STATIC_PATH',PUBLIC_PATH.'static'.DS);                       //图片JS及CSS的硬盘路径,是磁盘路径,不是浏览器的访问路径
		define('PLUGINS_PATH',ROOT_PATH.'plugins'.DS);                   //插件文件的硬盘路径
		define('CACHE_DIR',ROOT_PATH.'runtime'.DS);                         //缓存目录的硬盘路径
		define('QUN',cache('cache_modules_config') ? modules_config('qun')['name'] : '社群');       //圈子名称
		// 模板输出字符串内容替换
		$view_replace_str = [
		        '__UPLOADS__'=>PUBLIC_URL.'uploads',
		        '__STATIC__'=>PUBLIC_URL.'static',
		        '__DOMAIN__'=>request()->domain(),
		        '__QUN__'=>QUN,
		];
		define('ADMIN_FILENAME',config('admin.filename')?:'admin.php');     //后台入口文件名
		//存入配置文件方便调用
		config('view_replace_str',$view_replace_str);
		$module='';
		$dispatch=request()->dispatch();
		if(isset($dispatch['module'])){
			$module=$dispatch['module'][0];
		}
		$this->webdb=cache('webdb');
		if(empty($this->webdb)){
			$this->webdb=ConfigModel::getConfig();
			cache('webdb',$this->webdb);
		}
		if($this->webdb['www_url']){
			request()->domain($this->webdb['www_url']); //解决有的服务器无法识别https的问题,需要在后台定义域名网址
		}else{
			//空间不能识别https
			if(($_SERVER['HTTP_X_CLIENT_SCHEME']=='https'||$_SERVER['REDIRECT_HTTP_X_CLIENT_SCHEME']=='https')&&!strstr(request()->domain(),'https://')){
				request()->domain(str_replace('http://','https://',request()->domain()));
			}
		}
		//把相应的插件或频道模块的二维数组插入到一维数组去使用
		if($dispatch['module'][1]=='plugin'&&$dispatch['module'][2]=='execute'){
			$plugin_name=input('plugin_name');
			if($plugin_name&&is_array($this->webdb['P__'.$plugin_name])){
				$this->webdb=array_merge($this->webdb,$this->webdb['P__'.$plugin_name]);
			}
		}elseif($dispatch['module'][0]&&$this->webdb['M__'.$dispatch['module'][0]]){
			$this->webdb=array_merge($this->webdb,$this->webdb['M__'.$dispatch['module'][0]]);
		}
		$this->webdb['QB_VERSION']='X1.0';   //系统版本号
		config('webdb',$this->webdb);
		
		//md5style=xxx 前台风格预览
		if(input('get.md5style') && mymd5(input('get.md5style'),'DE')){
		    cookie('index_style',input('get.md5style'),1800);
		    delete_dir(RUNTIME_PATH.'temp');
		}
		
		if(cookie('index_style') && mymd5(cookie('index_style'),'DE')){
		    $index_style = mymd5(cookie('index_style'),'DE');              //前台风格预览演示
		}elseif(in_wap()){
		    $index_style = $this->webdb['wapstyle']?:'default';      //前台手机版风格
		}else{
		    if(IN_WAP===true){
		        $index_style = $this->webdb['wapstyle']?:'default';    //前台手机版风格
		    }else{
		        $index_style = $this->webdb['style']?:'default';          //前台电脑版风格
		    }
		}
		
		if(in_wap()){
			$member_style=$this->webdb['member_wapstyle']?:'default';  //手机会员中心风格
		}else{
			if(IN_WAP===true){
				$member_style=$this->webdb['member_wapstyle']?:'default';  //手机会员中心风格
			}else{
				$member_style=$this->webdb['member_style']?:'default';  //电脑会员中心风格
			}
		}

		$admin_style=$this->webdb['admin_style']?:'default';  //后台风格
		config('template.index_style',$index_style);
		config('template.member_style',$member_style);
		config('template.admin_style',$admin_style);
		//print_r($index_style);exit;
		if(empty(request()->root())){
			request()->root('/index.php');
		}
		if(in_array(ENTRANCE,['index','member','admin'])){    //设置模板独立目录
			config('template.view_base',TEMPLATE_PATH.ENTRANCE.'_style/'.config('template.'.ENTRANCE.'_style').'/');
			config('template.'.ENTRANCE.'_style')!='default'&&config('template.default_view_base',TEMPLATE_PATH.ENTRANCE.'_style/default/');
		}
		if(ENTRANCE==='admin'){
			if($module==''){
				header('Location: '.url('admin/index'));
				exit;
			}elseif($module!='admin'){
				// 定义模块的后台目录名
				config('url_controller_layer','admin');
				// 定义模块的后台模板路径目录
				config('template.view_path',APP_PATH.$module.'/view/admin/');
			}else{
				config('template.view_path',APP_PATH.$module.'/view/');
			}
		}elseif(ENTRANCE==='index'){
			if($module==''||$module=='index'){
				$module||$module='index';   //省略跳转处理
				config('template.view_path',APP_PATH.$module.'/view/'.$index_style.'/');
				$index_style=='default'||config('template.default_view_path',APP_PATH.$module.'/view/default/');
			}else{
			    if($this->webdb['hiden_index_php']){
			        \think\Url::root('/');  //隐藏index.php
			    }
				if(!modules_config($module)&&$module!='api'){
				    
				    //钩子可以方便扩展开发404跳转插件
					get_hook('cannot_find_module');
					hook_listen('cannot_find_module');		
					
					if(is_dir(APP_PATH.$module)){
						showerr('当前频道已关闭!');
					}else{
					    $array = @include(RUNTIME_PATH.'url_cfg.php');
					    if ($array[$module]) { //解决二级目录路由冲突的问题
					        header('location:'.url($array[$module]));
					        exit;
					    }
						showerr('当前频道不存在！',404);
					}
				}
				// 定义模块的前台文件目录
				config('url_controller_layer','index');
				// 定义模块的前台模板路径
				config('template.view_path',APP_PATH.$module.'/view/index/'.$index_style.'/');
				$index_style=='default'||config('template.default_view_path',APP_PATH.$module.'/view/index/default/');
			}			
		}elseif(ENTRANCE==='member'){
			if($module===''){
				header('Location: '.url('member/index'));
				exit;
			}elseif($module=='member'){
				config('template.view_path',APP_PATH.$module.'/view/'.$member_style.'/');
				$member_style=='default'||config('template.default_view_path',APP_PATH.$module.'/view/default/');
			}else{
				// 定义模块的会员中心目录名
				config('url_controller_layer','member');
				// 修改视图模板路径
				config('template.view_path',APP_PATH.$module.'/view/member/'.$member_style.'/');
				$member_style=='default'||config('template.default_view_path',APP_PATH.$module.'/view/member/default/');
			}
		}
		query("SET sql_mode=''");
	}
}