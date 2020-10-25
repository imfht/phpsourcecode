<?php 
namespace app\install\controller;

use think\Controller;
class Index extends Controller{
	public function __construct()
	{
		parent::__construct();
		if (!function_exists('curl_init'))
		{
			exit("您的主机不支持CURL函数，无法正常使用本插件！");
		}
		if (file_exists(__DIR__."/install.off"))
		{
			exit('软件已经被锁定，如需重装，请删除install.off文件');
		}
	}
	/**
	 * index
	 * @method 主方法； 流程控制
	 * @param number $wangwei
	 * @return mixed
	 */
	public function index($wangwei=1){
		global $empire,$editor,$ecms_config;
		$data['title'] = '安装/卸载 插件';
		if ($wangwei==1)
		{
			//安装第一步，用户协议
			if ( ! file_exists(APP_PATH.'/install/view/step1.php'))
			{
				// 找不到模板
				//show_404();
				echo '找不到模板';
			}
			return $this->fetch('./step1',$data);
		} 
		elseif ($wangwei == 2){
			//第二步
			return $this->fetch('./step2',$data);
		} elseif($wangwei==3){
			//判断并执行安装或卸载
			
			if(!empty($_POST['ecms']) && $_POST['ecms']=="install"){
				
				$doinstall=$_POST['doinstall'];
				$install = new \app\install\model\Index();
				if($doinstall=='install'){//安装操作
					
					$res=$install->install();
					if($res['errCode']){
						$this->error($res['errMsg'],NULL, '', 30);
						exit;
					}
					$lock = @fopen(__DIR__."/install.off", "w");
					 @fclose($lock);
					$word='已安装完毕!';
				}
				elseif($doinstall=='uninstall'){//卸载操作
					$install->uninstall();
					$word='已卸载完毕!';
				}
				$this->success($word);
			}else{
				return $this->fetch('./step1',$data);
			}
		}else{
		    exit("来源错误！");
		}
	}
	
}