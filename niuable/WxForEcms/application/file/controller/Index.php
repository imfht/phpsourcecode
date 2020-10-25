<?php
namespace app\file\controller;

use app\common\model\WxApp;
use app\file\model\WxFile;
use app\file\service\UpService;
use think\Controller;
use WxSDK\core\common\Ret;

/**
 * Index
 * @name 附件主类
 * @todo 获取附件信息；
 * @todo 处理（上传）附件；
 * @todo 显示附件；
 * @author WangWei
 *
 */
class Index extends Controller {
	// 传入视图的参数
	protected $data;
	// 公用数据获取类的实例
	protected $comm;
	// 输入的数据 array
	protected $in;
	public function __construct() {
		global $ecms_hashur, $lur, $isadmin;
		
		parent::__construct ();
		$ecms_hashur = isset ( $ecms_hashur ) ? $ecms_hashur : '';
		// 获取默认公众号数据;
		$common = new \app\common\controller\Index ();
		$this->comm = $common;
		$wx = $common->getDefaultWx ();
		if (! empty ( $wx ['errCode'] )) { // 获取失败时
			$this->error ( '中止操作：' . $wx ['errMsg'] );
		}
		$this->data = [ 
				'title' => '附件管理',
				'version' => config ( 'version' ),
				'ecms_hashur' => $ecms_hashur,
				'form_error' => array (),
				'public' => url ( '/', '', false ),
				'wx' => $wx ['data'],
				'aid' => $wx ['data'] ['id'],
				'lur' => $lur,
				'isadmin' => $isadmin 
		];
		$this->in = isset ( $_POST ) && count ( $_POST ) > 0 ? $_POST : $_GET;
		if (is_array ( $this->in ) && (count ( $this->in ) > 2 || isset ( $this->in ['page'] ))) {
			$this->data ['def'] = isset ( $this->in ['def'] ) && $this->in ['def'] == 1 ? 1 : 0;
		} else {
			$this->data ['def'] = 1;
		}
	}
	/**
	 * index
	 * @method 主方法；
	 * @todo 显示列表
	 * @return string
	 */
	public function index() {
		$data = $this->data;
		$in = $this->in;
		$data ['type'] = isset ( $in ['type'] ) ? $in ['type'] : - 1;
		// 排序参数
		$data ['order_by_time'] = isset ( $in ['order_by_time'] ) ? $in ['order_by_time'] : 'desc';
		$data ['order_by_size'] = isset ( $in ['order_by_size'] ) ? $in ['order_by_size'] : 'desc';
		$order = (isset ( $in ['order_by_time'] ) && ! empty ( $in ['order_by_time'] )) ? [ 
				'update_time' => $data ['order_by_time'],
				'size' => $data ['order_by_size'] 
		] : [ 
				'size' => $data ['order_by_size'] 
		];
		$my_query ['ecms_hashur'] = $data ['ecms_hashur'] ['href'];
		$my_query ['def'] = $data ['def'];
		if ($data ['def'])
			$where ['aid'] = $data ['aid'];
		if ($data ['type'] != - 1)
			$where ['type'] = $data ['type'];
		if (isset ( $in ['search'] )) {
			$where ['title|description'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		} else {
			$where = isset ( $where ) ? $where : array ();
		}
		// 获取数据
		$WxFile = new WxFile ();
		$list = $WxFile->where ( $where )->order ( $order )->paginate ( NULL, false, [ 
				'query' => $my_query,
				'path' => '' 
		] );
		$data ['page'] = $list->render ();
		$data ['list'] = $list;
		return $this->view ( './vIndex', $data );
	}
	/**
	 * fileList
	 * @method 列表;
	 * @todo 显示列表
	 * @param int $type
	 */
	public function fileList($type = -1) {
		$data = $this->data;
		$in = $this->in;
		
		$data ['type'] = isset ( $in ['type'] ) ? $in ['type'] : - 1;
		$WxWx = new \app\common\controller\Index ();
		$r = $WxWx->getDefaultWx ();
		if ($r ['errCode']) {
			$where = [ ];
		} else {
			$r = $r ['data'];
			$where ['aid'] = $r ['id'];
		}
		// 排序参数
		$data ['order_by_time'] = isset ( $in ['order_by_time'] ) ? $in ['order_by_time'] : 'desc';
		$data ['order_by_size'] = isset ( $in ['order_by_size'] ) ? $in ['order_by_size'] : 'desc';
		$order = (isset ( $in ['order_by_time'] ) && ! empty ( $in ['order_by_time'] )) ? [ 
				'update_time' => $data ['order_by_time'],
				'size' => $data ['order_by_size'] 
		] : [ 
				'size' => $data ['order_by_size'] 
		];
		$my_query ['order_by_time'] = $data ['order_by_time'];
		$my_query ['order_by_size'] = $data ['order_by_size'];
		$my_query ['ecms_hashur'] = $data ['ecms_hashur'] ['href'];
		$my_query ['def'] = $data ['def'];
		if ($data ['type'] != - 1)
			$where ['type'] = $data ['type'];
		if (isset ( $in ['search'] )) {
			$where ['title|description'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		} else {
			$where = isset ( $where ) ? $where : array ();
		}
		$WxFile = new WxFile ();
		$list = $WxFile->where ( $where )->order ( $order )->paginate ( 5, false, [ 
				'query' => $my_query,
				'path' => url ( 'file/index/fileList', '', false ) 
		] );
		$data ['list'] = $list;
		$data ['page'] = $list->render ();
		echo $this->fetch ( './list', $data );
	}
	/**
	 * add
	 * @method 新增附件
	 * @abstract 由Ueditor代替
	 */
	private function add() {
	}
	/**
	 * 更新视频的封面图片，用于客服消息
	 */
	public function updateTitleImg(){
	    $Wf = new WxFile();
	    $id = $_GET['id'];
	    $pathname = $_GET['path'];
	    $name = $_GET['name'];
	    $r = $Wf->get($id);
	    if(!$r){
	        $ret = new Ret('',NULL, 500, "资源不存在");
	        exit(json_encode($ret));
	    }
	    if(!$pathname || !$name){
	        $ret = new Ret('',NULL, 500, "参数错误");
	        exit(json_encode($ret));
	    }
	    
	    $path = str_replace('/'.$name,'', $pathname);
// 	    $this->success($name);
	    $thumb = $Wf->where([
	        'path'=>$path,
	        'name'=>$name,
	    ])->select();
	    
	    if(!$thumb){
	        $ret = new Ret('',NULL, 500, "资源不存在");
	        exit(json_encode($ret));
	    }
	    if(count($thumb) > 1){
	        $ret = new Ret('',NULL, 500, "数据异常");
	        exit(json_encode($ret));
	    }
	    $thumb = $thumb[0];
	    $r = $Wf->update([
	        'thumb_id'=>$thumb['id'],
	        'thumb'=>$pathname
	    ],['id'=>$id]);
	    if($r){
	        $ret = new Ret('',NULL, 0, "操作成功");
	        exit(json_encode($ret));
	    }
	    $ret = new Ret('',NULL, 500, "操作失败");
	    exit(json_encode($ret));
	}
	/**
	 * editor
	 * @method 编辑附件
	 * @todo 删、改附件
	 */
	public function editor() {
		$in = $this->in;
		$data = $this->data;
		$word = '';
		$res = [ 
				'errCode' => 1,
				'errMsg' => '系统错误，请稍后再试' 
		];
		if ('update' == $in ['editor_type']) {
			$word = '更新';
			$res = $this->updateDate ( $in, $data );
		} elseif ('up_to_wx' == $in ['editor_type']) {
			// 上传至微信端
			set_time_limit ( 0 );
			
			$word = '上传至微信';
			$id = $in ['id'] [$in ['site'] - 1];
			$WxFile = new WxFile ();
			$res = $WxFile->get ( $id );
			if (empty ( $res )) {
				return [ 
						'errCode' => 508,
						'errMsg' => '未找到相应附件' 
				];
			}
			$file = $res->toArray ();
			$file ['type'] = $this->transType ( $file ['type'] );
			$res = UpService::upForeverFile2Wx (new WxApp($this->data['aid']), $file );
			$res=[
			    'errCode' => $res->errCode,
			    'errMsg' => $res->errMsg
			];
		} elseif ('oneDelete' == $in ['editor_type']) {
			$word = '删除';
			$r = $this->getTheData ( $in ['site'] - 1 );
			$res = $this->oneDelete ( $r ['id'], 1 );
		} elseif ('sDelete' == $in ['editor_type']) {
			$word = '批量删除';
			foreach ( $in ['ids'] as $k => $v ) {
				$id [] = $in ['id'] [$v - 1];
			}
			
			$res = $this->sDelete ( $id, $data );
			if ($res ['errCode'] === 0) {
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作成功', url ( '/file/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			} elseif ($res ['errCode'] == 505) {
				// 错误页面的默认跳转页面是返回前一页，通常不需要设置
				$this->error ( $word . '操作失败:' . $res ['errMsg'], NULL, '', 10 );
			} else {
				$errid = $res ['data'];
				$strings = '';
				foreach ( $errid as $k => $v ) {
					$strings .= $v . '，';
				}
				$strings = rtrim ( $strings, '，' );
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作部分成功，出错的id有' . $strings, url ( '/file/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			}
		}
		if ($res ['errCode']) {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( $word . '操作失败' . ':' . $res ['errMsg'], NULL, NULL, - 1 );
		} else {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( $word . '操作成功', url ( '/file/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		}
	}
	/**
	 * getTheData
	 * @abstract 根据$s（位置）查找 多个数组中的值
	 * @param number $s 位置，从0开始计
	 * @return mixed[]
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
	 * @param array $in get或post传入的数据
	 * @param array $data 辅助参数
	 * @return mixed[]
	 */
	private function updateDate($in=[], $data=[]) {
		$res = $this->getTheData ( $in ['site'] - 1 );
		$result = $this->validate ( $res, 'Index' );
		if (true !== $result) { // 当验证不通过时
			return [ 
					'errCode' => 504,
					'errMsg' => '传入数据有误',
					'date' => $result 
			];
		} else {
			$WxFile = new WxFile ();
			$res = $WxFile->allowField ( true )->update ( $res );
			return [ 
					'errCode' => 0,
					'errMsg' => '成功',
					'date' => $res 
			];
		}
	}
	/**
	 * oneDelete
	 * @method 单个删除
	 * @param number $id
	 * @param number $must
	 * @return mixed[]
	 */
	private function oneDelete($id = 0, $must = 0) {
		if (empty ( $id )) {
			return [ 
					'errCode' => '501',
					'errMsg' => '附件id错误' 
			];
		}
		$WxFile = new WxFile ();
		$res = $WxFile->get ( $id );
		$path = ltrim ( $res ['path'], '/' );
		$path = ltrim ( $path, '\\' );
		$path = WEB_PATH . $path . '/' . $res ['name'];
		$path = str_replace ( array (
				'/',
				'\\' 
		), DIRECTORY_SEPARATOR, $path );
		$path = realpath ( $path );
		if ($path) { // 路径是否为空
			$result = @unlink ( $path );
			if ($result) {
				$res = $res->delete ();
				return [ 
						'errCode' => 0,
						'errMsg' => '文件删除成功' 
				];
			} else {
				if ($must) {
					$res = $res->delete ();
					return [ 
							'errCode' => 507,
							'errMsg' => '文件记录删除成功，但文件实体未被操作' 
					];
				} else {
					return [ 
							'errCode' => 503,
							'errMsg' => '文件实体删除失败' 
					];
				}
			}
		} else {
			if ($must) {
				$res = $res->delete ();
				return [ 
						'errCode' => 0,
						'errMsg' => '文件记录删除成功' 
				];
			} else {
				return [ 
						'errCode' => '502',
						'errMsg' => '路径错误' 
				];
			}
		}
	}
	/**
	 * sDelete
	 * @method 批量删除
	 * @param array $ids 附件id数组
	 * @param array $data 辅助参数
	 * @return mixed[] 操作结果
	 */
	private function sDelete($ids = [], $data = []) {
		$err = $success = 0;
		if (empty($ids)) {
			return [ 
					'errCode' => 1,
					'errMsg' => '未传入必须参数id值' 
			];
		} else {
			$err = $success = 0;
			$must = isset ( $data ['must'] ) ? $data ['must'] : 0;
			$errId = [ ];
			foreach ( $ids as $k => $v ) {
				$res = $this->oneDelete ( $v, $must );
				if ($res ['errCode']) { // 发生错误
					$err ++;
					$errId [] = $v;
				} else {
					$success ++;
				}
			}
			if ($err > 0 && $success == 0) {
				return [ 
						'errCode' => 505,
						'errMsg' => '糟糕，删除文件全部失败。需要认真检查错误原因，可能是因为数据库被篡改，导致文件路径出错。最后一个失败的原因是：' . $res ['errMsg'] 
				];
			} elseif ($err > 0 && $success > 0) {
				return [ 
						'errCode' => 0,
						'errMsg' => '删除文件成功，但不彻底，成功删除数量：' . $success . '，失败数：' . $err,
						'data' => $errId 
				];
			} else {
				return [ 
						'errCode' => 0,
						'errMsg' => '删除文件成功,成功数量：' . $success 
				];
			}
		}
	}
	
	
	
	/**
	 * getMediaInfo
	 * @todo 获取数据中是否已经有文件在微信端的数据
	 *
	 * @param array $r 获取到的附件数据
	 * @return mixed[] 操作结果
	 */
	private function getMediaInfo($r) {
		if (! $r)
			return [ 
					'errCode' => 508,
					'errMsg' => '未找到相应附件' 
			];
		if (! $r ['lifecycle'])
			return [ 
					'errCode' => - 1,
					'errMsg' => '未找到微信端数据' 
			];
		if ($r ['media_id']) {
			return [ 
					'errCode' => 0,
					'errMsg' => '成功在数据库中获取media_id',
					'data' => $r 
			];
		} else {
			return [ 
					'errCode' => - 1,
					'errMsg' => '未找到微信端数据' 
			];
		}
	}
	
	/**
	 * transType
	 * @todo 转换文件类型，将中文转换为英文
	 *
	 * @param string $type 类型
	 * @return string 英文版文件类型
	 */
	private function transType($type) {
		switch ($type) {
			case '图片' :
				$r = 'image';
				break;
			case '涂鸦' :
				$r = 'image';
				break;
			case '音频' :
				$r = 'voice';
				break;
			case '视频' :
				$r = 'video';
				break;
			case '其他' :
			default :
				$r = 'music'; // 待定
		}
		return $r;
	}
	/**
	 * view
	 * @method 显示模板
	 * @todo 用获得的数据渲染模板
	 * @todo 返回相应的数据
	 * @param String $temp 模板路径
	 * @param array $data 参数
	 * @return string 渲染后的模板数据
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
}
