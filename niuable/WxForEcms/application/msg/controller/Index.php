<?php
namespace app\msg\controller;

use think\Controller;
use app\msg\model\WxMsg;
use app\common\service\Tool;
use app\msgReply\model\WxMsgreply;

class Index extends Controller {
	// 传入视图的参数
	protected $data;
	
	// 输入的数据 array
	protected $in;
	public function __construct() {
		global $ecms_hashur;
		parent::__construct ();
		$ecms_hashur = isset ( $ecms_hashur ) ? $ecms_hashur : '';
		// 获取默认公众号信息（包含id），加入数据$data;
		$common = new \app\common\controller\Index ();
		$wx=$common->getDefaultWx();
		if (!empty($wx['errCode'])) {
			$this->error('中止操作：'.$wx['errMsg']);
		}
		$this->data = [ 
				'title' => '消息管理',
				'version' => config ( 'version' ),
				'ecms_hashur' => $ecms_hashur,
				'aid' => $wx['data']['id'],
				'wx' => $wx['data'],
				'form_error' => array (),
				'public' => url ( '/', '', false ) 
		];
		$this->in = isset ( $_POST ) && count ( $_POST ) > 0 ? $_POST : $_GET;
		if (is_array ( $this->in ) && (count ( $this->in ) > 2 || isset ( $this->in ['page'] ))) {
			$this->data ['def'] = isset ( $this->in ['def'] ) && $this->in ['def'] == 1 ? 1 : 0;
			$this->data ['is_hide_keyword'] = isset ( $this->in ['is_hide_keyword'] ) && $this->in ['is_hide_keyword'] == 1 ? 1 : 0;
		} else {
			$this->data ['def'] = 1;
			$this->data ['is_hide_keyword'] = 1;
		}
	}
	/**
	 * index
	 * @method 主函数
	 * @return string HTML代码
	 */
	public function index() {
		$data = $this->data;
		$in = $this->in;
		$data ['time'] = (isset ( $in ['time'] ) && $in ['time']) ? $in ['time'] : 0;
		$where = [ ];
		if (isset ( $data ['is_hide_keyword'] ) && $data ['is_hide_keyword']) {
			$where ['is_keyword'] = [ 
					'exp',
					'in (0) or isNull(is_keyword)'
			];
		}
		
		switch ($data ['time']) {
			case 1 :
				$where ['create_time'] = [ 
						'> time',
						'today' 
				];
				break;
			case '2' :
				$where ['create_time'] = [ 
						'between time',
						'yesterday,today' 
				];
				break;
			case '3' :
				$beforeYesterday = date ( 'Y-m-d', strtotime ( '-2 day' ) );
				$where ['create_time'] = [ 
						'between time',
						"$beforeYesterday,yesterday" 
				];
				break;
			case '4' :
				$beforeYesterday = date ( 'Y-m-d', strtotime ( '-2 day' ) );
				$where ['create_time'] = [ 
						'< time',
						$beforeYesterday 
				];
				break;
			case 0 :
			default :
		}
		$my_query ['is_hide_keyword'] = $data ['is_hide_keyword'];
		$my_query ['time'] = $data ['time'];
		$my_query ['ecms_hashur'] = $data ['ecms_hashur'] ['href'];
		$my_query ['def'] = $data ['def'];
		if($data['def']){
			$where['aid']=$data['aid'];
		}
		$my_query ['is_hide_keyword'] = $data ['is_hide_keyword'];
		if (isset ( $in ['search'] )) {
			$where ['name'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		}
		// 获取数据
		$WxMsg = new WxMsg ();
		$order='create_time desc';
		$list = $WxMsg->where ( $where )->order($order)->paginate ( '', false, [ 
				'query' => $my_query,
				'path' => '' 
		] );
		
		$data ['page'] = $list->render ();
		$res = Tool::transURL($list, $this->data["aid"]);
		if(isset($res["errCode"]) && $res["errCode"] != 0){
			$list = [];
		}else{
			$list = isset($res["data"])?$res["data"]:[];
			foreach ($list as $k=>$v){
				$user=new \app\user\controller\Index();
				$res=$user->getTheUser($v['user_name']);
				if ($res['errCode']) {
					continue;
				}
				$v['user']=$res['data'];
				$list[$k]=$v;
			}
		}
		$data['list']=$list;
		return $this->view ( './vIndex', $data );
	}
	/**
	 * add
	 * @method 新增消息
	 * @abstract 新增操作会在系统与微信通信中执行
	 * @return string
	 */
	private function add() {
	}
	/**
	 * editor
	 * @method 编辑
	 * 
	 * 
	 */
	public function editor() {
		$in = $this->in;
		$data = $this->data;
		$word = '';
		$res = '';
		if ('update' == $in ['editor_type']) {
			$word = '更新';
			$res = $this->updateDate ( $in, $data );
		} elseif ('oneDelete' == $in ['editor_type']) {
			$word = '删除';
			$res = $this->oneDelete ( $in, $in ['site'] - 1 );
		} elseif ('sDelete' == $in ['editor_type']) {
			$word = '批量删除';
			$res = $this->sDelete ( $in, $data );
			if ($res [0] == 1) {
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作成功', url ( '/msg/index', '', false ) . '?' . $data ['ecms_hashur'] ['href'] );
			} elseif ($res [0] == 0) {
				// 错误页面的默认跳转页面是返回前一页，通常不需要设置
				$this->error ( $word . '操作失败' );
			} else {
				$errid = $res [1];
				$strings = '';
				foreach ( $errid as $k => $v ) {
					$strings .= $v . '，';
				}
				$strings = rtrim ( $strings, '，' );
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作部分成功，出错的id有' . $strings, url ( '/msg/index', '', false ) . '?' . $data ['ecms_hashur'] ['href'] );
			}
		} else if('clearOldMsg' == $in['editor_type']){
			$WxMsg = new WxMsg();
			$time = time()-60*60*24*5;
			$msgs = $WxMsg->where([
				'aid'=> $data['aid'],
				'create_time' => ['<', $time],
			])->select();
			$res = true;
			foreach($msgs as $k => $msg){
				$reply = new WxMsgreply();
				$ret = $reply->where([
					'aid' => $this->data['aid'],
					'msg_id'=>$msg['id']
				])->delete();
				if(!$msg->delete()){
					$res = false;
					break;
				}
			}
			if($res){
				$this->success ( $word . '操作成功', url ( '/msg/index', '', false ) . '?' . $data ['ecms_hashur'] ['href'] );
			}else{
				$this->error ( '操作失败' );
			}
		}
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( $word . '操作成功', url ( '/msg/index', '', false ) . '?' . $data ['ecms_hashur'] ['href'] );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( $word . '操作失败' );
		}
	}
	/**
	 * getTheData
	 * @method 获取数据二维数据组中同角标元素
	 * @param number $s 位置
	 * @return mixed[] 数据
	 */
	private function getTheData($s = 0) {
		$r = [ ];
		$in = $this->in;
		unset ( $in ['ids'] );
		foreach ( $in as $k => $v ) {
			if (is_array ( $v )) {
				$r [$k] = $v [$s];
			}
		}
		
		return $r;
	}
	/**
	 * updateDate
	 * @method 更新
	 * @param array $in 数据
	 * @param array $data 参数
	 * @return boolean 是否成功
	 */
	private function updateDate($in, $data) {
		$res = $this->getTheData ( $in ['site'] - 1 );
		$result = $this->validate ( $res, 'Index' );
		if (true !== $result) { // 当验证不通过时
			return false;
		} else {
			$WxMsg = new WxMsg ();
			$res = $WxMsg->allowField ( true )->update ( $res );
			return true;
		}
	}
	/**
	 * oneDelete
	 * @method 单个删除
	 * @param array $in 数据
	 * @param number $s 位置
	 * @return number
	 */
	private function oneDelete($in, $s) {
		$r = $this->getTheData ( $s );
		$WxMsg = new WxMsg ();
		$reply = new WxMsgreply();
		$ret = $reply->where([
			'msg_id'=>$r['id']
		])->delete();
		if(!$ret){
			return false;
		}
		return $WxMsg->get ( $r ['id'] )->delete ();
	}
	/**
	 * sDelete
	 * @method 批量删除
	 * @param array $in 数据
	 * @param array $data 参数
	 * @return mixed[] 操作结果
	 */
	private function sDelete($in, $data) {
		$error = $success = 0;
		foreach ( $in ['ids'] as $key => $val ) {
			$res = $this->oneDelete ( $in, $val - 1 );
			if ($res) {
				$success ++;
			} else {
				$error ++;
				$err_id [] = $in ['id'] [$val - 1];
			}
		}
		if ($error < 1) {
			return [ 
					1 
			];
		} elseif ($success < 1) {
			return [ 
					0 
			];
		} else {
			return [ 
					- 1,
					$err_id 
			];
		}
	}
	/**
	 * view
	 * @method 显示
	 * @param string $temp 模板路径
	 * @param array $data 数据
	 * @return string HTML代码
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
}
