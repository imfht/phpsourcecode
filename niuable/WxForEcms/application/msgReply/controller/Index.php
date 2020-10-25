<?php 
namespace app\msgreply\controller;
use think\Controller;

class Index extends Controller
{
	//传入视图的参数
    protected $data;
    
    //输入的数据 array
    protected $in;
	/**
	 *构造函数
	 *获取配置、数据；
	 *数据初始化 
	 */
	public function __construct(){
		global $ecms_hashur;
		parent::__construct();

		$ecms_hashur = isset($ecms_hashur)?$ecms_hashur:'';
		//获取默认公众号aid，加入数据$r;
		$common=new \app\common\controller\Index();
		$wx=$common->getDefaultWx();
		if (!empty($wx['errCode'])) {
			$this->error('中止操作：'.$wx['errMsg']);
		}
		
		$this->data = [
				'title'			=>'回复管理',
				'version'		=>config('version'),
				'ecms_hashur'	=>$ecms_hashur,
				'form_error'	=>array(),
				'public'		=>url('/','',false),
				'wx'			=>$wx['data'],
				'aid'			=>$wx['data']['id'],
		];
		$this->in=isset($_POST) && count($_POST)>0?$_POST:$_GET;
		if(is_array($this->in) && (count($this->in)>2 || isset($this->in['page']) )){
			$this->data['def']=isset($this->in['def']) && $this->in['def']==1?1:0;
			$this->data['panel']=isset($this->in['panel'])?$this->in['panel']:1;
		}else{
			$this->data['def']=1;
			$this->data['panel']=1;
		}
    }

}
