<?php
namespace app\view\controller;

use think\Controller;
use app\news\model\WxNews;
use app\common\model\WxWx;

class Index extends Controller {
	// 传入视图的参数
	protected $data;
	// 公用数据获取类的实例
	protected $comm;
	// 输入的数据 array
	protected $in;
	// 图文块模型实例
	protected $WxNews;
	public function __construct() {
		global $ecms_hashur, $lur, $isadmin;
		parent::__construct ();
		$this->data = [ 
				'version' => config ( 'version' ),
				'public' => url ( '/', '', false ),
		];
		$this->in = $_GET;
	}
	/**
	 * index
	 * @method 主方法，显示主页
	 *
	 * @return string 渲染后的页面
	 */
	public function index() {
		$in=$this->in;
		$data=$this->data;
		//验证参数
		$res=$this->validate($in, 'Index');
		if($res===true){
			
			$this->WxNews=$WxNews=new WxNews();
			$res=$WxNews->get($in['id']);
			if($res){
				$res=$res->toArray();
				if($res["is_open_outside"]==1){
					$this.redirect($res["outside_url"]);
					return;
				}
				$data=array_merge($data,$res);
				//获取微信公众号数据
				$WxWx=new WxWx();
				$res=$WxWx->visible(['id','name'])->find($res['aid']);
				$data['wx']=$res;
				return $this->view('./vIndex',$data);
			}else{
				$this->error();
			}
		}else{
			$this->error();
		}
	}

	/**
	 * view
	 * @method 渲染模板并返回结果
	 *
	 * @param string $temp 模板路径
	 * @param mixed[] $data 数据
	 * @return string HTML代码
	 */
	private function view($temp, $data) {
		return $this->fetch ( $temp, $data );
	}
}
