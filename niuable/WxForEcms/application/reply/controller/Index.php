<?php
namespace app\reply\controller;

use think\Controller;
use app\reply\model\WxReply;
use app\file\model\WxFile;
use app\news\model\WxNews;

class Index extends Controller {
	// 传入视图的参数
	protected $data;
	
	// 公用数据获取类的实例
	protected $comm;
	// 输入的数据 array
	protected $in;
	/**
	 * 构造函数
	 * 获取配置、数据；
	 * 数据初始化
	 */
	public function __construct() {
		global $ecms_hashur;
		parent::__construct ();
		
		$ecms_hashur = isset ( $ecms_hashur ) ? $ecms_hashur : '';
		// 获取默认公众号aid，加入数据$r;
		$common = new \app\common\controller\Index ();
		$this->comm = $common;
		$wx = $common->getDefaultWx ();
		if (! empty ( $wx ['errCode'] )) {
			$this->error ( '中止操作：' . $wx ['errMsg'] );
		}
		
		$this->data = [ 
				'title' => '回复管理',
				'version' => config ( 'version' ),
				'ecms_hashur' => $ecms_hashur,
				'form_error' => array (),
				'public' => url ( '/', '', false ),
				'wx' => $wx ['data'],
				'aid' => $wx ['data'] ['id'] 
		];
		$this->in = isset ( $_POST ) && count ( $_POST ) > 0 ? $_POST : $_GET;
		
		if (is_array ( $this->in ) && (count ( $this->in ) > 2 || isset ( $this->in ['page'] ))) {
			$this->data ['def'] = isset ( $this->in ['def'] ) && $this->in ['def'] == 1 ? 1 : 0;
			$this->data ['panel'] = isset ( $this->in ['panel'] ) ? $this->in ['panel'] : 1;
		} else {
			$this->data ['def'] = 1;
			$this->data ['panel'] = 1;
		}
		$this->in ['aid'] = isset ( $this->in ['aid'] ) ? $this->in ['aid'] : $this->data ['aid'];
	}
	/**
	 * index
	 * @method 主函数
	 *
	 * @return string 封面页
	 */
	public function index() {
		$data = $this->data;
		$in = $this->in;
		$where ['type'] = $data ['panel'] == 4 ? NULL : $data ['panel'];
		$my_query [] = $data ['ecms_hashur'] ['href'];
		$my_query ['def'] = $data ['def'];
		if (isset ( $in ['search'] ) && (! empty ( $in ['search'] ) || $in ['search'] === 0)) {
			$where ['keyword'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		}
		// 是否只显示默认公众号（隐藏其他）
		if ($data ['def'])
			$where ['aid'] = $data ['aid'];
			// 获取数据
		$WxReply = new WxReply ();
		$list = $WxReply->where ( $where )->paginate ( '', false, [ 
				'query' => $my_query,
				/*
				 * 保持链接稳定，尤其是修改等操作后跳转至本函数时
				 */
				'path' => '' 
		] );
		
		if ($data ['panel'] == 1) {
			$data ['list'] = $list;
			$data ['page'] = $list->render ();
			$data ['reply_content'] = '';
		} else {
			if (! empty ( $list )) {
				$res = $list->toArray ();
				if (! empty ( $res ['data'] ))
					$res = $res ['data'] [0];
				else
					$res = [ ];
			} else {
				$res = [ ];
			}
			if (empty ( $res )) {
				$msg_type = 'text';
			} else {
				$msg_type = $res ['msg_type'];
				$res = $this->getReplyContent ( $res );
			}
			
			$data = array_merge ( $data, $res );
			$data ['msg_type'] = isset ( $in ['msg_type'] ) ? $in ['msg_type'] : $msg_type;
			$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
		}
		
		return $this->view ( './vIndex', $data );
	}
	
	/**
	 * add
	 * @method 新增回复
	 *
	 * @access public
	 * @param int $reply_type 回复类型 （1关键词回复，2关注时回复，3无匹配回复）
	 * @return string HTML
	 */
	public function add($reply_type = NULL) {
		$data = $this->data;
		$r = $this->in;
		$r ['type'] = ($reply_type && $reply_type != 4) ? $reply_type : 1;
		$reply_type = $reply_type ? $reply_type : '';
		$data ['panel'] = $reply_type == 1 ? 4 : $reply_type;
		$r ['aid'] = isset ( $r ['aid'] ) ? $r ['aid'] : $data ['aid'];
		// 验证数据合法性
		if ($reply_type == 1)
			$result = $this->validate ( $r, 'Index' );
		else
			$result = $this->validate ( $r, 'Index.NotKeyword' );
		if (true !== $result) { // 当验证不通过时
			$data ['form'] = $r;
			$data = array_merge ( $data, $r );
			$data ['def'] = 1; // 辅助参数，默认是只显示默认公众号
			$data ['error_msg'] = "<h3><p class=text-danger>保存失败：数据不符合要求，请修改后重试</p></h3>";
			$data ['form_error'] = $result;
			$data ['msg_type'] = $r ['msg_type'];
			
			$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
			return $this->view ( './vIndex', $data );
		}
		
		$r ['aid'] = $data ['aid'];
		$WxReply = new WxReply ();
		$res = $WxReply->allowField ( true )->save ( $r );
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( '新增成功', url ( '/reply/Index/index/', '', false ) . '?panel=' . $reply_type . $data ['ecms_hashur'] ['href'] . '&def=1' );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( '新增失败' );
		}
	}
	/**
	 * editor
	 * @method 编辑回复
	 * @return String HTML代码
	 */
	public function editor($reply_type = 1) {
		$in = $this->in;
		$data = $this->data;
		$data ['panel'] = $reply_type;
		if (empty ( $in ['id'] ))
			$this->error ();
		$word = '';
		// 验证数据合法性
		if ($reply_type == 1)
			$result = $this->validate ( $in, 'Index' );
		else
			$result = $this->validate ( $in, 'Index.NotKeyword' );
		if (true !== $result) { // 当验证不通过时
			$data ['form'] = $in;
			$data = array_merge ( $data, $in );
			$data ['def'] = 1; // 辅助参数，默认是只显示默认公众号
			$data ['error_msg'] = "<h3><p class=text-danger>保存失败：数据不符合要求，请修改后重试</p></h3>";
			$data ['form_error'] = $result;
			$data ['msg_type'] = $in ['msg_type'];
			
			$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
			if(1==$reply_type){
				$data ['panel'] = 5; // 关键词修改
			}
			return $this->view ( './vIndex', $data );
		}
		if (! isset ( $in ['editor_type'] )) {
			$word = '编辑';
			if ($reply_type != 1) {
				$res = $this->updateDate ( $in, 1 );
			} else {
				$res = $this->updateDate ( $in );
			}
			if ($res ['errCode']) {
				$data ['form'] = $in;
				$data ['error_msg'] = "<h3><p class=text-danger>保存失败：数据不符合要求，请修改后重试</p></h3>";
				$data ['form_error'] = $res ['data'];
				$data ['msg_type'] = $in ['msg_type'];
				$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
				return $this->view ( './vIndex', $data );
			}
		} elseif ('keyword' == $in ['editor_type']) { // 关键词编辑页更新
			$word = '更新';
			$res = $this->updateDate ( $in, $data );
		} elseif ('update' == $in ['editor_type']) { // 关键词列表页更新
			$word = '更新';
			$r = $this->getTheData ( $in ['site'] - 1 );
			$res = $this->updateDate ( $r );
		} elseif ('delete' == $in ['editor_type']) {
			$word = '删除';
			$res = $this->oneDelete ( $in, $in ['site'] - 1 );
		} elseif ('sDelete' == $in ['editor_type']) {
			$word = '批量删除';
			$res = $this->sDelete ( $in, $data );
			if ($res [0] == 1) {
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作成功', url ( '/reply/index', '', false ) . '?' . $data ['ecms_hashur'] ['href'] );
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
				$this->success ( $word . '操作部分成功，出错的id有' . $strings, url ( '/reply/index', '', false ) . '?' . $data ['ecms_hashur'] ['href'] );
			}
		}
		if ($res ['errCode']) {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( $word . '操作失败' );
		} else {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( $word . '操作成功', url ( '/reply/index', '', false ) . "?" . $data ['ecms_hashur'] ['href'] . '&def=1' );
		}
	}
	/**
	 * find
	 * @method 查询
	 * @todo 获取指定id的回复数据
	 * @param number $id 数据记录id
	 * @return string HTML代码
	 */
	public function find($id = NULL) {
		$data = $this->data;
		if ($id) {
			$WxReply = new WxReply ();
			$r = $WxReply->get ( $id );
			if (empty ( $r )) {
				$this->error ( '未找到此记录，id:' . $id );
			} else {
				$r = $r->toArray ();
				$r = $this->getReplyContent ( $r );
				$data = array_merge ( $data, $r );
				
				$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
				$data ['panel'] = 5; // 修改
				return $this->view ( './vIndex', $data );
			}
		} else {
			$this->error ( '系统打了个盹，请稍后再试~' );
		}
	}
	/**
	 * getTheData
	 * 获取数组中的特定位置数据
	 * @param number $s 位置
	 * @return mixed[] 新数据
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
	 * @param array|number $data 辅助参数
	 * @return mixed[] 操作结果
	 */
	private function updateDate($in = NULL, $data = NULL) {
		$in = $in ? $in : $this->in;
		$data = $data ? $data : $this->data;
		if ($data === 1) {
			$result = $this->validate ( $in, 'Index.NotKeyword' );
			if (true !== $result) { // 当验证不通过时
				return [ 
						'errCode' => 307,
						'errMsg' => '传参错误',
						'data' => $result 
				];
			} else {
				$WxReply = new WxReply ();
				$res = $WxReply->allowField ( true )->isUpdate ( true )->save ( $in );
				return [ 
						'errCode' => 0,
						'errMsg' => '成功' 
				];
			}
		} else {
			$result = $this->validate ( $in, 'Index' );
			if (true !== $result) { // 当验证不通过时
				return [ 
						'errCode' => 307,
						'errMsg' => '传参错误',
						'data' => $result 
				];
			} else {
				$WxReply = new WxReply ();
// 				exit(json_encode($in));
				$res = $WxReply->allowField ( true )->isUpdate ( true )->save ( $in );
				return [ 
						'errCode' => 0,
						'errMsg' => '成功' 
				];
			}
		}
	}
	/**
	 * oneDelete
	 * @method 单个删除
	 * @param array $in 数据
	 * @param number $s 位置
	 * @return number 影响数量
	 */
	private function oneDelete($in, $s) {
		$r = $this->getTheData ( $s );
		$WxReply = new WxReply ();
		return $result = $WxReply->get ( $r ['id'] )->delete ();
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
	/**
	 * getReplyContent
	 * @method 获取回复的具体内容
	 * @param array $r 查询/转换 参数
	 * @return mixed[] 待回复数据
	 */
	private function getReplyContent($r = NULL) {
		$comm = $this->comm;
		if ($r ['msg_type'] == 'news') {
			$news = $r ['news'];
			$News = new \app\news\controller\Index ();
			foreach ( $news as $k => $v ) {
				$res = $News->getTheNews ( $v );
				if (! $res ['errCode'])
					$news [$k] = $res ['data'];
			}
			$r ['news'] = $news;
		} elseif ($r ['msg_type'] == 'img') {
			$WxFile = new WxFile ();
			$res = $WxFile->get ( $r ['img'] );
			if (empty ( $res )) {
				$d['img_url'] = ''; // 赋空值，不进行中断操作，以便用户修改
				$d['img_title']='';
			} else {
				$img = $res->toArray ();
				$d ['img_title'] = $comm->my_substr ( $img ['title'], 0, 10 );
				$d ['img_url'] = $img ['path'] . '/' . $img ['name'];
			}
			$r['img'] = $d;
		} elseif ($r ['msg_type'] == 'video') {
			$WxFile = new WxFile ();
			$res = $WxFile->get ( $r ['video'] );
			if ($res) {
				$video = $res->toArray ();
				$r ['video_title'] = $comm->my_substr ( $video ['title'], 0, 10 );
				$r ['video_url'] = $video ['path'] . '/' . $video ['name'];
			} else {
				$r ['video_url'] = ''; // 赋空值，不进行中断操作，以便用户修改
			}
		} elseif ($r ['msg_type'] == 'voice') {
			$WxFile = new WxFile ();
			$res = $WxFile->get ( $r ['voice'] );
			if ($res) {
				$voice = $res->toArray ();
				$r ['voice_title'] = $comm->my_substr ( $voice ['title'], 0, 10 );
				$r ['voice_url'] = $voice ['path'] . '/' . $voice ['name'];
			} else {
				$r ['voice_url'] = ''; // 赋空值，不进行中断操作，以便用户修改
			}
		}
		
		return $r;
	}
}

